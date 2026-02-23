<?php

namespace Modules\AutoAudit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Contact;
use App\Product;
use App\Audit;
use App\Category;
use App\BusinessLocation;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\AutoAudit\Entities\AutoAudit;
use Modules\AutoAudit\Entities\AutoAuditCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\AutoAudit\Entities\AutoAuditSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use Maatwebsite\Excel\Concerns\ToArray;
use Modules\MiniReportB1\Services\GoogleVisionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;




class AutoAuditController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $googleVisionService;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil,
        GoogleVisionService $googleVisionService
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
        $this->businessUtil = $businessUtil;
        $this->productUtil = $productUtil;
        $this->googleVisionService = $googleVisionService;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'autoaudit')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.autoaudit')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_autoaudit = AutoAudit::where('business_id', $business_id)->count();

        $total_autoaudit_category = AutoAuditCategory::where('business_id', $business_id)->count();

        $autoaudit_category = DB::table('autoaudit_main as autoaudit')
            ->leftJoin('autoaudit_category as autoauditcategory', 'autoaudit.category_id', '=', 'autoauditcategory.id')
            ->select(
                DB::raw('COUNT(autoaudit.id) as total'),
                'autoauditcategory.name as category'
            )
            ->where('autoaudit.business_id', $business_id)
            ->groupBy('autoauditcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('autoaudit::AutoAudit.dashboard')
            ->with(compact('total_autoaudit', 'total_autoaudit_category', 'autoaudit_category', 'module'));
    }

    public function destroy($id)
    {
        try {
            AutoAudit::destroy($id);
            return response()->json(['success' => true, 'msg' => __('autoaudit::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'autoaudit')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.autoaudit')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = AutoAuditCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AutoAudit-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('AutoAudit-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('autoaudit::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'autoaudit')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.autoaudit')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('autoaudit::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new AutoAuditCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AutoAuditCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('autoaudit::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'autoaudit')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.autoaudit')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = AutoAuditCategory::find($id);
        return view('autoaudit::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = AutoAuditCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AutoAuditCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('autoaudit::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            AutoAuditCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('autoaudit::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

}
