<?php

namespace Modules\Backup\Http\Controllers;

use App\Business;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PDO;
use PDOException;


class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'BackupLanguage']);
    }

    public function index(Request $request)
    {
        // Fix: Use proper config path
        $backupConfig = config('backup.backup_path', 'backups');
        $diskConfig = config('backup.disk', 'local');

        $files = Storage::disk($diskConfig)->files($backupConfig);
        $files = array_filter($files, fn($file) => str_starts_with(basename($file), 'db_backup_'));
        rsort($files);

        $backups = array_map(function ($file) use ($diskConfig) {
            return [
                'filename' => basename($file),
                'size' => Storage::disk($diskConfig)->size($file),
                'created_at' => Storage::disk($diskConfig)->lastModified($file),
                'downloadUrl' => route('backup.backup.download', basename($file)),
                'deleteUrl' => route('backup.backup.delete', basename($file)),
            ];
        }, $files);

        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedBackups = array_slice($backups, $offset, $perPage);
        $backupsPaginated = new LengthAwarePaginator(
            $paginatedBackups,
            count($backups),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $tables = DB::select('SHOW TABLES');
        $tables = array_map(fn($table) => array_values((array)$table)[0], $tables);
        $businesses = Business::all();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'backups' => $backups,
            ]);
        }

        return view('backup::Backup.index', ['backups' => $backupsPaginated, 'tables' => $tables, 'businesses' => $businesses]);
    }

    public function backup(Request $request)
    {
        $request->validate([
            'table' => 'nullable|string|regex:/^[a-zA-Z0-9_]+$/',
            'custom_query' => 'nullable|string',
            'custom_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'business' => 'nullable|integer|exists:business,id',
        ]);

        $table = $request->input('table');
        $customQuery = $request->input('custom_query');
        $filename = 'db_backup_' . ($table ? $table . '_' : '') . now()->format('Ymd_His') . '.sql';

        // Fix: Use fallback config values
        $backupPath = config('backup.backup_path', 'backups');
        $diskName = config('backup.disk', 'local');
        $path = $backupPath . '/' . $filename;
        $disk = Storage::disk($diskName);

        try {
            // Ensure the backup directory exists
            if (!$disk->exists($backupPath)) {
                $disk->makeDirectory($backupPath);
            }

            // Build the final SELECT query
            $finalQuery = null;
            $bindings = [];

            if ($customQuery) {
                // Use custom query as-is
                $finalQuery = $customQuery;
            } elseif ($table) {
                // Build query from form inputs
                $finalQuery = "SELECT * FROM {$table}";
                $wheres = [];

                // Business filter
                if ($request->filled('business')) {
                    $wheres[] = 'business_id = ?';
                    $bindings[] = $request->business;
                }

                // Date filter
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $wheres[] = 'created_at BETWEEN ? AND ?';
                    $bindings[] = $request->start_date . ' 00:00:00';
                    $bindings[] = $request->end_date . ' 23:59:59';
                } elseif ($request->filled('start_date')) {
                    $wheres[] = 'created_at >= ?';
                    $bindings[] = $request->start_date . ' 00:00:00';
                }

                if ($wheres) {
                    $finalQuery .= ' WHERE ' . implode(' AND ', $wheres);
                }

                // Limit
                if ($request->filled('custom_limit')) {
                    $finalQuery .= ' LIMIT ' . (int)$request->custom_limit;
                } elseif ($request->limit && $request->limit !== 'none') {
                    $finalQuery .= ' LIMIT ' . (int)$request->limit;
                }
            }

            $sqlContent = $this->generateBackupSql(
                $table ? [$table] : [],
                $finalQuery,
                $bindings
            );

            // Write the backup file
            $disk->put($path, $sqlContent);

            if (!$disk->exists($path)) {
                throw new \Exception('Backup file creation failed.');
            }

            // Get updated backup list
            $files = $disk->files($backupPath);
            $files = array_filter($files, fn($file) => str_starts_with(basename($file), 'db_backup_'));
            rsort($files);
            $backups = array_map(function ($file) use ($diskName) {
                return [
                    'filename' => basename($file),
                    'size' => Storage::disk($diskName)->size($file),
                    'created_at' => Storage::disk($diskName)->lastModified($file),
                    'downloadUrl' => route('backup.backup.download', basename($file)),
                    'deleteUrl' => route('backup.backup.delete', basename($file)),
                ];
            }, $files);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup created successfully.',
                    'backups' => $backups,
                    'downloadUrl' => route('backup.backup.download', $filename),
                ]);
            }

            session()->flash('success', 'Backup created successfully.');
            return $disk->download($path, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('backup.backup.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    protected function generateBackupSql(array $tables, ?string $customQuery = null, array $bindings = []): string
    {
        // phpMyAdmin-style header
        $sqlContent = "-- phpMyAdmin SQL Dump\n";
        $sqlContent .= "-- version 5.2.1\n";
        $sqlContent .= "-- https://www.phpmyadmin.net/\n";
        $sqlContent .= "--\n";
        $sqlContent .= "-- Host: 127.0.0.1\n";
        $sqlContent .= "-- Generation Time: " . now()->format('M d, Y \a\t h:i A') . "\n";
        $sqlContent .= "-- Server version: 10.4.32-MariaDB\n";
        $sqlContent .= "-- PHP Version: 8.2.12\n\n";

        // Initial SET statements
        $sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sqlContent .= "START TRANSACTION;\n";
        $sqlContent .= "SET time_zone = \"+00:00\";\n\n";

        // Character set settings
        $sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $sqlContent .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $sqlContent .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

        // Database name
        $databaseName = config('database.connections.mysql.database');
        $sqlContent .= "--\n";
        $sqlContent .= "-- Database: `$databaseName`\n";
        $sqlContent .= "--\n\n";

        if ($customQuery) {
            try {
                // Validate that it's a SELECT statement
                if (!Str::startsWith(strtoupper(trim($customQuery)), 'SELECT')) {
                    throw new \Exception('Custom query must be a SELECT statement.');
                }

                // Execute the query
                $results = DB::select($customQuery, $bindings);

                if (empty($results)) {
                    $sqlContent .= "-- Custom Query returned no data\n";
                    $sqlContent .= "-- Query: " . $customQuery . "\n\n";
                    return $sqlContent . "COMMIT;\n";
                }

                // Extract table name from query
                preg_match('/FROM\s+`?([a-zA-Z0-9_]+)`?/i', $customQuery, $matches);
                $tableName = $matches[1] ?? null;

                if ($tableName && Schema::hasTable($tableName)) {
                    $sqlContent .= "-- --------------------------------------------------------\n\n";
                    $sqlContent .= "-- Table structure for table `$tableName`\n";
                    $sqlContent .= "--\n\n";
                    $sqlContent .= $this->getTableStructure($tableName);
                    $sqlContent .= "\n";
                    $sqlContent .= "-- Dumping data for table `$tableName`\n";
                    $sqlContent .= "-- Query: " . $customQuery . "\n";
                    $sqlContent .= "--\n\n";

                    // Generate REPLACE INTO statements from query results
                    if (!empty($results)) {
                        $columns = array_keys((array) $results[0]);
                        $sqlContent .= "REPLACE INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES\n";

                        $rows = [];
                        foreach ($results as $row) {
                            $values = array_map(function ($value) {
                                return is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                            }, (array) $row);
                            $rows[] = "(" . implode(', ', $values) . ")";
                        }
                        $sqlContent .= implode(",\n", $rows) . ";\n\n";
                    }
                } else {
                    $sqlContent .= "-- Could not determine table for custom query results\n";
                    $sqlContent .= "-- Query: " . $customQuery . "\n\n";
                }
            } catch (\Exception $e) {
                Log::error('Custom query failed: ' . $e->getMessage());
                throw new \Exception('Invalid custom query: ' . $e->getMessage());
            }
        } else {
            // Process specified tables
            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    Log::warning("Table '$table' does not exist, skipping.");
                    continue;
                }

                $sqlContent .= "-- --------------------------------------------------------\n\n";
                $sqlContent .= "-- Table structure for table `$table`\n";
                $sqlContent .= "--\n\n";
                $sqlContent .= $this->getTableStructure($table);
                $sqlContent .= "\n";
                $sqlContent .= "-- Dumping data for table `$table`\n";
                $sqlContent .= "--\n\n";
                $sqlContent .= $this->getTableData($table);
                $sqlContent .= "\n";
            }
        }

        $sqlContent .= "COMMIT;\n\n";
        $sqlContent .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $sqlContent .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $sqlContent .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

        return $sqlContent;
    }

    protected function getTableStructure(string $table): string
    {
        try {
            $createTable = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            if (!$createTable) {
                throw new \Exception("Could not get CREATE TABLE statement for table: $table");
            }

            // Get the create table SQL
            $createTableSql = $createTable->{'Create Table'};

            // Modify CREATE TABLE to include IF NOT EXISTS
            $createTableSql = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $createTableSql);

            return $createTableSql . ";\n";
        } catch (\Exception $e) {
            Log::error("Failed to get table structure for '$table': " . $e->getMessage());
            return "-- Error getting table structure for `$table`: " . $e->getMessage() . "\n";
        }
    }

    protected function getTableData(string $table): string
    {
        try {
            $rows = DB::table($table)->get();

            if ($rows->isEmpty()) {
                return "-- No data in table `$table`\n";
            }

            $columns = array_keys((array) $rows[0]);
            // Changed from INSERT INTO to REPLACE INTO
            $sql = "REPLACE INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";

            $rowData = [];
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                }, (array) $row);
                $rowData[] = "(" . implode(', ', $values) . ")";
            }
            $sql .= implode(",\n", $rowData) . ";\n";

            return $sql;
        } catch (\Exception $e) {
            Log::error("Failed to get table data for '$table': " . $e->getMessage());
            return "-- Error getting data for table `$table`: " . $e->getMessage() . "\n";
        }
    }


    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:10240', // 10MB limit
            'business_identifier' => 'required|string', // Added for business selection
        ]);

        try {
            // Fetch business_id
            $businessIdentifier = $request->input('business_identifier');
            $business = Business::where('id', $businessIdentifier)
                ->orWhere('name', $businessIdentifier)
                ->first();

            if (!$business) {
                throw new \Exception("Business '$businessIdentifier' not found.");
            }
            $businessId = $business->id;

            $file = $request->file('backup_file');
            $sqlContent = $file->get();

            // Validate SQL content
            if (empty(trim($sqlContent))) {
                throw new \Exception('The uploaded SQL file is empty.');
            }

            // Check if it's a valid SQL dump
            if (!Str::contains($sqlContent, ['CREATE TABLE', 'INSERT INTO', 'REPLACE INTO'])) {
                throw new \Exception('The uploaded file does not appear to be a valid SQL dump.');
            }

            // Normalize line endings and clean content
            $sqlContent = str_replace(["\r\n", "\r"], "\n", $sqlContent);

            // Remove comments more thoroughly
            $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
            $sqlContent = preg_replace('/^#.*$/m', '', $sqlContent);
            $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

            // Split by semicolons but handle semicolons within quotes
            $statements = $this->splitSqlStatements($sqlContent);

            if (empty($statements)) {
                throw new \Exception('No valid SQL statements found in the uploaded file.');
            }

            Log::info('Total statements to process: ' . count($statements));

            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Set SQL mode for compatibility
            DB::statement('SET SESSION sql_mode = ""');

            // Use try-finally to ensure cleanup

            // Process statements
            $successCount = 0;
            $errorCount = 0;

            foreach ($statements as $index => $statement) {
                $statement = trim($statement);

                if (empty($statement)) {
                    continue;
                }

                // Skip problematic statements
                if (preg_match('/^\s*(START\s+TRANSACTION|COMMIT|ROLLBACK|SET\s+@|LOCK\s+TABLES|UNLOCK\s+TABLES)/i', $statement)) {
                    Log::debug("Skipping statement $index: " . substr($statement, 0, 100));
                    continue;
                }

                // Skip DROP statements for safety
                if (preg_match('/^\s*DROP\s+(TABLE|DATABASE|INDEX|VIEW)\s+/i', $statement)) {
                    Log::warning("Skipping DROP statement $index for safety: " . substr($statement, 0, 100));
                    continue;
                }

                // Handle CREATE TABLE statements - execute as is
                if (preg_match('/^\s*CREATE\s+TABLE/i', $statement)) {
                    try {
                        DB::statement($statement);
                        $successCount++;
                        Log::debug("Executed CREATE TABLE statement $index");
                    } catch (\Exception $e) {
                        // Table might already exist, log but continue
                        Log::warning("CREATE TABLE statement $index failed (might already exist): " . $e->getMessage());
                    }
                    continue;
                }

                // Handle INSERT/REPLACE statements for tables with business_id
                if (preg_match('/^(INSERT|REPLACE)\s+INTO\s+[`"]?([^`"\s]+)[`"]?\s*/i', $statement, $matches)) {
                    $tableName = $matches[2];

                    // Check if table exists and has business_id column
                    if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'business_id')) {
                        if ($this->processBusinessIdStatement($statement, $tableName, $businessId, $index)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        continue;
                    }
                }

                // Execute other statements as-is
                try {
                    DB::statement($statement);
                    $successCount++;
                    Log::debug("Executed statement $index: " . substr($statement, 0, 100));
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Failed to execute statement $index: " . substr($statement, 0, 200) . ' | Error: ' . $e->getMessage());
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $message = "Import completed. Success: $successCount, Errors: $errorCount";
            Log::info($message);

            if ($errorCount > 0 && $successCount === 0) {
                throw new \Exception('All statements failed to execute. Check the logs for details.');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'stats' => [
                        'success' => $successCount,
                        'errors' => $errorCount,
                        'total' => count($statements)
                    ]
                ]);
            }

            return redirect()->route('backup.backup.index')->with('success', $message);
        } catch (\Exception $e) {
            // Ensure foreign key checks are re-enabled on error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $cleanupError) {
                Log::error('Failed to re-enable foreign key checks: ' . $cleanupError->getMessage());
            }

            Log::error('Import failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('backup.backup.index')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }


    /**
     * Split SQL content into individual statements, handling quoted strings properly
     */
    private function splitSqlStatements($sqlContent)
    {
        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = '';
        $escaped = false;

        $lines = explode("\n", $sqlContent);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || preg_match('/^(--|#)/', $line)) {
                continue;
            }

            for ($i = 0; $i < strlen($line); $i++) {
                $char = $line[$i];

                if ($escaped) {
                    $escaped = false;
                    $currentStatement .= $char;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    $currentStatement .= $char;
                    continue;
                }

                if (!$inString && ($char === '"' || $char === "'")) {
                    $inString = true;
                    $stringChar = $char;
                    $currentStatement .= $char;
                } elseif ($inString && $char === $stringChar) {
                    $inString = false;
                    $stringChar = '';
                    $currentStatement .= $char;
                } elseif (!$inString && $char === ';') {
                    $statement = trim($currentStatement);
                    if (!empty($statement)) {
                        $statements[] = $statement;
                    }
                    $currentStatement = '';
                } else {
                    $currentStatement .= $char;
                }
            }

            if (!$inString) {
                $currentStatement .= "\n";
            }
        }

        // Add final statement if exists
        $statement = trim($currentStatement);
        if (!empty($statement)) {
            $statements[] = $statement;
        }

        return $statements;
    }

    /**
     * Process INSERT/REPLACE statements for tables with business_id
     */
    private function processBusinessIdStatement($statement, $tableName, $businessId, $index)
    {
        try {
            // Extract columns and values using more robust parsing
            if (!preg_match('/\(([^)]+)\)\s*VALUES?\s*(.+)$/is', $statement, $matches)) {
                Log::warning("Could not parse INSERT statement $index for table `$tableName`");
                return false;
            }

            $columnsStr = $matches[1];
            $valuesStr = $matches[2];

            // Parse columns
            $columns = array_map(function ($col) {
                return trim($col, '` "');
            }, explode(',', $columnsStr));

            $businessIdIndex = array_search('business_id', $columns);

            if ($businessIdIndex === false) {
                // No business_id column in this statement, execute as-is
                DB::statement($statement);
                return true;
            }

            // Parse VALUES - handle multiple value sets
            preg_match_all('/\(([^)]+)\)/', $valuesStr, $valueMatches);

            foreach ($valueMatches[1] as $valueString) {
                $values = $this->parseValues($valueString);

                if (count($values) !== count($columns)) {
                    Log::warning("Column count mismatch in table `$tableName` at statement $index");
                    continue;
                }

                // Build record array
                $record = array_combine($columns, $values);
                $record['business_id'] = $businessId; // Override business_id

                // Get primary key
                $primaryKey = $this->getPrimaryKey($tableName);

                if (!isset($record[$primaryKey])) {
                    Log::warning("Primary key `$primaryKey` not found in record for table `$tableName`");
                    continue;
                }

                // Handle record insertion/update with business_id consideration
                $this->handleRecordUpsert($tableName, $record, $primaryKey, $businessId);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to process business_id statement for table `$tableName`, statement $index: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse VALUES string handling quoted values properly
     */
    private function parseValues($valueString)
    {
        $values = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';
        $escaped = false;

        for ($i = 0; $i < strlen($valueString); $i++) {
            $char = $valueString[$i];

            if ($escaped) {
                $current .= $char;
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $current .= $char;
                $escaped = true;
                continue;
            }

            if (!$inQuotes && ($char === '"' || $char === "'")) {
                $inQuotes = true;
                $quoteChar = $char;
            } elseif ($inQuotes && $char === $quoteChar) {
                $inQuotes = false;
                $quoteChar = '';
            } elseif (!$inQuotes && $char === ',') {
                $values[] = trim($current, ' \'"');
                $current = '';
                continue;
            }

            $current .= $char;
        }

        // Add the last value
        if (trim($current) !== '') {
            $values[] = trim($current, ' \'"');
        }

        return $values;
    }

    /**
     * Get primary key for a table
     */
    private function getPrimaryKey($tableName)
    {
        try {
            $primaryKeys = Schema::getConnection()
                ->getDoctrineTable($tableName)
                ->getPrimaryKey()
                ->getColumns();

            return $primaryKeys[0] ?? 'id';
        } catch (\Exception $e) {
            Log::warning("Could not determine primary key for table `$tableName`, defaulting to 'id'");
            return 'id';
        }
    }

    /**
     * Handle record upsert with business_id consideration
     */
    private function handleRecordUpsert($tableName, $record, $primaryKey, $businessId)
    {
        try {
            // Check if record exists with same ID and business_id
            $existingRecord = DB::table($tableName)
                ->where($primaryKey, $record[$primaryKey])
                ->where('business_id', $businessId)
                ->first();

            if ($existingRecord) {
                // Record exists with same ID and business_id - update it
                DB::table($tableName)
                    ->where($primaryKey, $record[$primaryKey])
                    ->where('business_id', $businessId)
                    ->update($record);
                Log::info("Updated existing record with {$primaryKey}: {$record[$primaryKey]} for business_id: {$businessId} in table `$tableName`");
            } else {
                // Check if record exists with same ID but different business_id
                $recordWithSameId = DB::table($tableName)
                    ->where($primaryKey, $record[$primaryKey])
                    ->first();

                if ($recordWithSameId && $recordWithSameId->business_id != $businessId) {
                    // Record exists with same ID but different business_id - create new record
                    $newRecord = $record;
                    unset($newRecord[$primaryKey]); // Let it auto-increment

                    DB::table($tableName)->insert($newRecord);
                    Log::info("Created new record (original {$primaryKey}: {$record[$primaryKey]}) for business_id: {$businessId} in table `$tableName`");
                } else {
                    // No record exists with this ID - insert with original ID
                    DB::table($tableName)->insert($record);
                    Log::info("Inserted new record with {$primaryKey}: {$record[$primaryKey]} for business_id: {$businessId} in table `$tableName`");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to upsert record in table `$tableName`: " . $e->getMessage());
            throw $e;
        }
    }


    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'backup_file' => 'required|file|max:10240', // 10MB limit
    //     ]);

    //     try {
    //         $file = $request->file('backup_file');
    //         $sqlContent = $file->get();

    //         // Validate SQL content
    //         if (empty(trim($sqlContent))) {
    //             throw new \Exception('The uploaded SQL file is empty.');
    //         }

    //         // Check if it's a valid SQL dump - updated to include REPLACE INTO
    //         if (!Str::contains($sqlContent, ['CREATE TABLE', 'INSERT INTO', 'REPLACE INTO'])) {
    //             throw new \Exception('The uploaded file does not appear to be a valid SQL dump.');
    //         }

    //         // Simplified SQL statement splitting
    //         $sqlContent = str_replace(["\r\n", "\r"], "\n", $sqlContent);

    //         // Remove comments more safely
    //         $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
    //         $sqlContent = preg_replace('/^#.*$/m', '', $sqlContent);
    //         $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

    //         // Split by semicolons (simple approach)
    //         $statements = array_filter(
    //             array_map('trim', explode(';', $sqlContent)),
    //             function ($stmt) {
    //                 return !empty($stmt);
    //             }
    //         );

    //         if (empty($statements)) {
    //             throw new \Exception('No valid SQL statements found in the uploaded file.');
    //         }

    //         Log::info('Total statements to process: ' . count($statements));

    //         // Disable foreign key checks temporarily
    //         DB::statement('SET FOREIGN_KEY_CHECKS=0');

    //         // Set SQL mode for compatibility
    //         DB::statement('SET SESSION sql_mode = ""');

    //         // Process statements
    //         $successCount = 0;
    //         $errorCount = 0;

    //         foreach ($statements as $index => $statement) {
    //             $statement = trim($statement);

    //             if (empty($statement)) {
    //                 continue;
    //             }

    //             // Skip problematic statements
    //             if (preg_match('/^\s*(START\s+TRANSACTION|COMMIT|ROLLBACK|SET\s+@)/i', $statement)) {
    //                 Log::debug("Skipping statement $index: " . substr($statement, 0, 100));
    //                 continue;
    //             }

    //             // Skip DROP statements for safety (optional)
    //             if (preg_match('/^\s*DROP\s+(TABLE|DATABASE)\s+/i', $statement)) {
    //                 Log::warning("Skipping DROP statement $index for safety: " . substr($statement, 0, 100));
    //                 continue;
    //             }

    //             try {
    //                 // Execute each statement individually
    //                 DB::statement($statement);
    //                 $successCount++;
    //             } catch (\Exception $e) {
    //                 $errorCount++;
    //                 Log::error("Failed to execute statement $index: " . substr($statement, 0, 200) . ' | Error: ' . $e->getMessage());
    //             }
    //         }

    //         // Re-enable foreign key checks
    //         DB::statement('SET FOREIGN_KEY_CHECKS=1');

    //         $message = "Import completed. Success: $successCount, Errors: $errorCount";
    //         Log::info($message);

    //         if ($errorCount > 0 && $successCount === 0) {
    //             throw new \Exception('All statements failed to execute. Check the logs for details.');
    //         }

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => $message,
    //             ]);
    //         }

    //         return redirect()->route('backup.backup.index')->with('success', $message);
    //     } catch (\Exception $e) {
    //         Log::error('Import failed: ' . $e->getMessage());

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Import failed: ' . $e->getMessage(),
    //             ], 500);
    //         }

    //         return redirect()->route('backup.backup.index')->with('error', 'Import failed: ' . $e->getMessage());
    //     }
    // }

    public function download(Request $request, $filename)
    {
        $backupPath = config('backup.backup_path', 'backups');
        $diskName = config('backup.disk', 'backup');
        $path = $backupPath . '/' . $filename;

        if (!Storage::disk($diskName)->exists($path)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found.',
                ], 404);
            }
            return redirect()->route('backup.backup.index')->with('error', 'Backup file not found.');
        }

        return Storage::disk($diskName)->download($path, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function delete(Request $request, $filename)
    {
        $backupPath = config('backup.backup_path', 'backups');
        $diskName = config('backup.disk', 'backup');
        $path = $backupPath . '/' . $filename;

        if (!Storage::disk($diskName)->exists($path)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found.',
                ], 404);
            }
            return redirect()->route('backup.backup.index')->with('error', 'Backup file not found.');
        }

        Storage::disk($diskName)->delete($path);

        $files = Storage::disk($diskName)->files($backupPath);
        $files = array_filter($files, fn($file) => str_starts_with(basename($file), 'db_backup_'));
        rsort($files);
        $backups = array_map(function ($file) use ($diskName) {
            return [
                'filename' => basename($file),
                'size' => Storage::disk($diskName)->size($file),
                'created_at' => Storage::disk($diskName)->lastModified($file),
                'downloadUrl' => route('backup.backup.download', basename($file)),
                'deleteUrl' => route('backup.backup.delete', basename($file)),
            ];
        }, $files);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully.',
                'backups' => $backups,
            ]);
        }
        return redirect()->route('backup.backup.index')->with('success', 'Backup deleted successfully.');
    }

    public function export(Request $request)
    {
        try {
            // Get the authenticated user's business
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated.');
            }

            $business = Business::where('id', $user->business_id)->first();
            if (!$business) {
                throw new \Exception('Business not found for the authenticated user.');
            }
            $businessId = $business->id;

            // Get database configuration from .env
            $dbConfig = config('database.connections.mysql');
            $host = $dbConfig['host'];
            $database = $dbConfig['database'];
            $port = $dbConfig['port'] ?? 3306;
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];

            // Verify database connection
            try {
                $pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$database}",
                    $username,
                    $password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                throw new \Exception('Failed to connect to the database: ' . $e->getMessage());
            }

            // Get backup configuration
            $backupPath = config('backup.backup_path', 'backups');
            $diskName = config('backup.disk', 'local');
            $disk = Storage::disk($diskName);
            $fallbackBackupDir = 'C:/Temp/backups';
            $useFallback = false;

            // Check primary disk and path
            $primaryFullPath = $disk->path($backupPath);
            if (!$disk->exists($backupPath)) {
                try {
                    $disk->makeDirectory($backupPath);
                    Log::info("Created backup directory on {$diskName} disk: {$primaryFullPath}");
                } catch (\Exception $e) {
                    Log::warning("Failed to create backup directory on {$diskName} disk: {$primaryFullPath}, Error: {$e->getMessage()}");
                }
            }

            // Check if primary path is writable
            if (!$disk->exists($backupPath) || !is_writable($primaryFullPath)) {
                Log::warning("Primary backup path is not accessible or writable: {$primaryFullPath}");
                $useFallback = true;
                $backupPath = str_replace('\\', '/', str_replace('C:/Temp/', '', $fallbackBackupDir));
                $primaryFullPath = $fallbackBackupDir;

                // Create and verify fallback directory
                if (!file_exists($fallbackBackupDir)) {
                    if (!mkdir($fallbackBackupDir, 0755, true) || !is_dir($fallbackBackwardupDir)) {
                        throw new \Exception("Failed to create fallback backup directory: {$fallbackBackupDir}");
                    }
                    Log::info("Created fallback backup directory: {$fallbackBackupDir}");
                }
                if (!is_writable($fallbackBackupDir)) {
                    throw new \Exception("Fallback backup directory is not writable: {$fallbackBackupDir}");
                }
            }

            // Get all tables
            $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

            // Initialize SQL dump content
            $sqlDump = "-- Database Export for Business ID: {$businessId}\n";
            $sqlDump .= "-- Exported on: " . now()->toDateTimeString() . "\n";
            $sqlDump .= "-- Generated by: " . $user->name . "\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sqlDump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sqlDump .= "SET time_zone = \"+00:00\";\n\n";

            foreach ($tables as $table) {
                // Check if table has business_id column
                $columns = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
                $hasBusinessId = false;
                foreach ($columns as $column) {
                    if ($column['Field'] === 'business_id') {
                        $hasBusinessId = true;
                        break;
                    }
                }

                // Get table structure
                $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC)['Create Table'];
                $sqlDump .= "\n-- Table structure for `$table`\n";
                $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlDump .= $createTable . ";\n\n";

                // Get table data
                $query = $hasBusinessId
                    ? "SELECT * FROM `$table` WHERE business_id = :businessId"
                    : "SELECT * FROM `$table`";

                $stmt = $pdo->prepare($query);
                if ($hasBusinessId) {
                    $stmt->bindValue(':businessId', $businessId);
                }
                $stmt->execute();

                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($rows) > 0) {
                    $sqlDump .= "-- Data for `$table`\n";

                    // Get column names
                    $columns = array_map(function ($col) {
                        return $col['Field'];
                    }, $columns);
                    $columnsString = '`' . implode('`,`', $columns) . '`';

                    // Process rows in chunks
                    $chunkSize = 100;
                    $chunks = array_chunk($rows, $chunkSize);

                    foreach ($chunks as $chunk) {
                        $values = [];
                        foreach ($chunk as $row) {
                            $rowValues = [];
                            foreach ($columns as $column) {
                                $value = $row[$column];
                                if (is_null($value)) {
                                    $rowValues[] = 'NULL';
                                } elseif (is_numeric($value)) {
                                    $rowValues[] = $value;
                                } else {
                                    $rowValues[] = "'" . addslashes($value) . "'";
                                }
                            }
                            $values[] = '(' . implode(',', $rowValues) . ')';
                        }

                        $sqlDump .= "INSERT INTO `$table` ($columnsString) VALUES\n";
                        $sqlDump .= implode(",\n", $values) . ";\n";
                    }

                    $sqlDump .= "\n";
                }
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Generate filename
            $filename = 'backup_' . $database . '_' . now()->format('Ymd_His') . '.sql';
            $filePath = $backupPath . '/' . $filename;
            $fullPath = rtrim($primaryFullPath, '/') . '/' . $filename;

            // Write file to disk
            if ($useFallback) {
                if (file_put_contents($fullPath, $sqlDump) === false) {
                    throw new \Exception("Failed to write backup file to fallback path: {$fullPath}");
                }
            } else {
                if (!$disk->put($filePath, $sqlDump)) {
                    throw new \Exception("Failed to write backup file to {$diskName} disk: {$fullPath}");
                }
            }

            // Verify file exists
            if (!file_exists($fullPath)) {
                throw new \Exception("Backup file could not be created at: {$fullPath}");
            }

            // Log the export
            Log::info("Database export successful for business_id: {$businessId} by user: {$user->id}, file: {$fullPath}, disk: " . ($useFallback ? 'fallback' : $diskName));

            // Return the file as a download response
            return response()->download(
                $fullPath,
                $filename,
                ['Content-Type' => 'application/sql']
            )->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Export failed: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}
