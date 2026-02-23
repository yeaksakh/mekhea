<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class YlLangController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function YlLang($moduleName, $title)
    {
        $routesPath = base_path("Modules/{$moduleName}/Resources/lang/kh/lang.php");
        $moduleNameLower = strtolower($moduleName);

        $processedTitles = []; // Array to hold the processed titles
        // Remove "b numbers" from the module name
        $module = preg_replace('/B\d+/', '', $moduleName);

        if (!is_null($title) && is_array($title)) {
            foreach ($title as $index) {
                if (!is_null($index)) {
                    $cleanedTitle = preg_replace('/_\d+$/', '', $index);
                    $processedTitles[] = "'{$index}' => '{$cleanedTitle}',";                }
            }
        }

        $titledata = implode("\n", $processedTitles);

        $newContent = <<<EOT
        <?php

        return [
            'dashboard' => 'Dashboard',
            '{$moduleName}' => '{$module}',
            'total_{$moduleName}_category' => 'Total Categories',
            'total_{$moduleName}' => 'Total {$module}',
            '{$moduleName}_category' => 'Category',
            'all_{$moduleName}' => 'All {$module}',
            'add_{$moduleName}' => 'Add {$module}',
            'edit_{$moduleNameLower}' => 'Edit {$module}',
            '{$moduleNameLower}' => '{$module}',
            'title' => 'Title',
            'add_category' => 'Add Category',
            'string' => 'String',
            'category' => 'Category',
            'create_by' => 'Create By',
            'assign_to' => 'Assign To',
            'supplier_id' => 'Supplier',
            'customer_id' => 'Customer',
            'product_id' => 'Product',
            'date' => 'Date',
            'description' => 'Description',
            'all_category' => 'All Category',
            'name' => 'Name',
            'setting' => 'Setting',
            {$titledata}
        ];
        EOT;

        $this->files->put($routesPath, $newContent);
        Log::info("Routes file created: {$routesPath}");
    }
}
