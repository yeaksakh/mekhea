<?php

namespace Modules\ModuleCreateModule\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use App\Utils\ModuleUtil;
use App\Http\Controllers\Controller;

class ApiModuleController extends Controller 
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $modules = ModuleCreator::where('business_id', $business_id)->get(); // Use get() to retrieve results

        return response()->json($modules);
    }
}