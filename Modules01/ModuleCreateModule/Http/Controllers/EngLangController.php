<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class EngLangController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function EngLang($moduleName, $title)
    {
        $moduleNameLower = strtolower($moduleName);

        $processedTitles = []; // Array to hold the processed titles
        // Remove "b numbers" from the module name
        $module = preg_replace('/B\d+/', '', $moduleName);

        if (!is_null($title) && is_array($title)) {
            foreach ($title as $index) {
                if (!is_null($index)) {
                    $cleanedTitle = preg_replace('/_\d+$/', '', $index);
                    $processedTitles[] = "'{$index}' => '{$cleanedTitle}',";
                }
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
            '{$moduleNameLower}_created' => 'New {$module} has been created',
            '{$moduleNameLower}_module' => '{$module}',
            'title' => 'Title',
            'key' => 'Keywords',
            'telegram' => 'Telegram',
            'add_category' => 'Add Category',
            'category' => 'Category',
            'create_by' => 'Create By',
            'assign_to' => 'Assign To',
            'supplier_id' => 'Supplier',
            'customer_id' => 'Customer',
            'product_id' => 'Product',
            'date' => 'Date',
            'qrcode' => 'QRCode',
            'createdby' => 'Created By',
            'createdat' => 'Created At',
            'description' => 'Description',
            'all_category' => 'All Category',
            'name' => 'Name',
            'setting' => 'Setting',
            'language_settings' => 'Language Setting',
            'update_language' => 'Update Language',
            'permission_settings' => 'Permission Setting',
            'select_language' => 'Select language',
            'language_keys' => 'Language',
            'translation' => 'Translation',
            'save_translations' => 'Save Translations',
            'audit' => 'Audit',
            'audit_status' => 'Audit Status',
            'audit_note' => 'Audit Note',
            'image' => 'Image',
            'no' => 'No',
            'yes' => 'Yes',
            'pending' => 'Pending',
            'accept' => 'Accept',  
            'cancel' => 'Cancel',  
            'low' => 'Low',        
            'medium' => 'Medium',  
            'high' => 'High',      
            'urgent' => 'Urgent',  
            'partial' => 'Partial', 
            'paid' => 'Paid',      
            'due' => 'Due',        
            'order' => 'Order',    
            'packed' => 'Packed',  
            'shipped' => 'Shipped', 
            'delivered' => 'Delivered', 
            'returned' => 'Returned',
            'details' => 'Details',
            'filter' => 'Filter',
            {$titledata}
        ];
        EOT;

        $newContentKh = <<<EOT
        <?php

        return [
            'dashboard' => 'ផ្ទាំងគ្រប់គ្រង',
            '{$moduleName}' => '{$module}',
            'total_{$moduleName}_category' => 'ចំនួនប្រភេទសរុប',
            'total_{$moduleName}' => 'សរុប {$module}',
            '{$moduleName}_category' => 'ប្រភេទ',
            'all_{$moduleName}' => '{$module} ទាំងអស់',
            'add_{$moduleName}' => 'បន្ថែម {$module}',
            'edit_{$moduleNameLower}' => 'កែប្រែ {$module}',
            '{$moduleNameLower}' => '{$module}',
            '{$moduleNameLower}_created' => '{$module} បានបង្កើតថ្មី',
            '{$moduleNameLower}_module' => '{$module}',
            'title' => 'ចំណងជើង',
            'telegram' => 'Telegram',
            'add_category' => 'បន្ថែមប្រភេទ',
            'key' => 'ពាក្យគន្លឹះ',
            'category' => 'ប្រភេទ',
            'create_by' => 'បង្កើតដោយ',
            'assign_to' => 'ចាត់តាំងទៅកាន់',
            'supplier_id' => 'អ្នកផ្គត់ផ្គង់',
            'customer_id' => 'អតិថិជន',
            'product_id' => 'ផលិតផល',
            'date' => 'កាលបរិច្ឆេទ',
            'description' => 'ការពិពណ៌នា',
            'all_category' => 'ប្រភេទទាំងអស់',
            'name' => 'ឈ្មោះ',
            'setting' => 'ការកំណត់',
            'language_settings' => 'ការកំណត់ភាសា',
            'update_language' => 'ធ្វើបច្ចុប្បន្នភាពភាសា',
            'permission_settings' => 'ការកំណត់សិទ្ធិ',
            'select_language' => 'ជ្រើសរើសភាសា',
            'language_keys' => 'ភាសា',
            'translation' => 'ការបកប្រែ',
            'save_translations' => 'រក្សាទុក',
            'audit' => 'សវនកម្',
            'audit_status' => 'ស្ថានភាពសវនកម្ម',
            'audit_note' => 'ចំណាំសវនកម្',
            'image' => 'រូបភាព',
            'no' => 'ទេ',
            'yes' => 'បាទ/ចាស',
            'pending' => 'កំពុងរង់ចាំ',
            'accept' => 'ទទួលយក',  
            'cancel' => 'បោះបង់',  
            'low' => 'ទាប',        
            'medium' => 'មធ្យម',  
            'high' => 'ខ្ពស់',      
            'urgent' => 'បន្ទាន់',  
            'partial' => 'ដោយផ្នែក', 
            'paid' => 'បានបង់',      
            'due' => 'នៅជំពាក់',        
            'order' => 'ការកម្មង់',    
            'packed' => 'បានវេចខ្ចប់',  
            'shipped' => 'បានដឹកជញ្ជូន', 
            'delivered' => 'បានផ្ញើរួច', 
            'returned' => 'បានត្រឡប់មកវិញ',
            'details' => 'លំអិត',
            'filter' => 'តម្រង',
            {$titledata}
        ];
        EOT;

        // Define paths for 'en' and 'kh' language files
        $paths = base_path("Modules/{$moduleName}/Resources/lang/en/lang.php");
        $pathskh = base_path("Modules/{$moduleName}/Resources/lang/kh/lang.php");

        // Create the files
        $this->files->put($paths, $newContent);
        $this->files->put($pathskh, $newContentKh);

    }
}
