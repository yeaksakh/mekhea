<?php

namespace Modules\Backup\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Modules\Products\Entities\Product as EntitiesProduct;
use ZipArchive;

class BackupProductsCommand extends Command
{
    protected $signature = 'backup:products';
    protected $description = 'Generate a downloadable backup of the products table as a JSON file';

    public function handle()
    {
        try {
            $data = [];
            EntitiesProduct::chunk(1000, function ($products) use (&$data) {
                $data = array_merge($data, $products->toArray());
            });

            $filename = 'products_backup_' . now()->format('Ymd_His') . '.json';
            $path = config('backup.backup_path') . '/' . $filename;
            $content = json_encode($data, JSON_PRETTY_PRINT);

            if (config('backup.encrypt')) {
                $content = Crypt::encryptString($content);
            }

            if (config('backup.compress')) {
                $zipFilename = str_replace('.json', '.zip', $filename);
                $zipPath = config('backup.backup_path') . '/' . $zipFilename;
                $tempFile = storage_path('app/temp/' . $filename);

                Storage::disk('local')->put('temp/' . $filename, $content);

                $zip = new ZipArchive;
                if ($zip->open(storage_path('app/' . $zipPath), ZipArchive::CREATE) === true) {
                    $zip->addFile($tempFile, $filename);
                    $zip->close();
                    Storage::disk('local')->delete('temp/' . $filename);
                    $path = $zipPath;
                }
            } else {
                Storage::disk(config('backup.disk'))->put($path, $content);
            }

            $this->cleanupOldBackups();

            $this->info("Products backup created: " . basename($path));
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
        }
    }

    protected function cleanupOldBackups()
    {
        $files = Storage::disk(config('backup.disk'))->files(config('backup.backup_path'));
        $files = array_filter($files, fn($file) => str_starts_with(basename($file), 'products_backup_'));
        rsort($files);

        if (count($files) > config('backup.max_backups')) {
            foreach (array_slice($files, config('backup.max_backups')) as $file) {
                Storage::disk(config('backup.disk'))->delete($file);
            }
        }
    }
}
