<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller;

class DeletecontrollerModuleController extends Controller
{
    protected $files;

    public function deleteController($moduleName)
    {
        // Determine controller file path
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/{$moduleName}Controller.php");

        // Check if controller file exists and delete it
        if ($this->files->exists($controllerPath)) {
            $this->files->delete($controllerPath);
            return redirect()->back()->with('success', 'Controller deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Controller file not found.');
        }
    }
}
