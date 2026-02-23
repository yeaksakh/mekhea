<?php

namespace Modules\Documentary\Http\Controllers;

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
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\Documentary\Entities\Documentary;
use Modules\Documentary\Entities\DocumentaryCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\Documentary\Entities\DocumentarySocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class DocumentaryController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) || !auth()->user()->can('superadmin') || !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_documentary = Documentary::where('business_id', $business_id)->count();

        $total_documentary_category = DocumentaryCategory::where('business_id', $business_id)->count();

        $documentary_category = DB::table('documentary_main as documentary')
            ->leftJoin('documentary_category as documentarycategory', 'documentary.category_id', '=', 'documentarycategory.id')
            ->select(
                DB::raw('COUNT(documentary.id) as total'),
                'documentarycategory.name as category'
            )
            ->where('documentary.business_id', $business_id)
            ->groupBy('documentarycategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('documentary::Documentary.dashboard')
            ->with(compact('total_documentary', 'total_documentary_category', 'documentary_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $query = Documentary::where('business_id', $business_id)->orderBy('id', 'desc');

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $query->whereDate('created_at', '>=', $request->start_date)
                    ->whereDate('created_at', '<=', $request->end_date);
            }

            if (!empty($request->category_id)) {
                $query->where('category_id', $request->category_id);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Documentary.show', $row->id) . '" data-container="#Documentary_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Documentary.edit', $row->id) . '" data-container="#Documentary_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= '<button class="btn btn-xs btn-danger delete-Documentary" data-href="' . route('Documentary.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->addColumn('subcategory', function ($row) {
                    $subcategory = DocumentaryCategory::find($row->category_id);
                    return $subcategory ? $subcategory->name : '-';
                })
                ->addColumn('category', function ($row) {
                    $subcategory = DocumentaryCategory::find($row->category_id);
                    return $subcategory && $subcategory->parent ? $subcategory->parent->name : '-';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    return $user ? $user->first_name . ' ' . $user->last_name : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $users = User::forDropdown($business_id, false, true, true);
        $category = DocumentaryCategory::forDropdown($business_id);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('documentary::Documentary.index')->with(compact(
            'module',
            'leads',
            'users',
            'customer',
            'product',
            'supplier',
            'business_locations',
            'category',
            'departments',
            'designations'
        ));
    }

    public function getSubcategories(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $parent_id = $request->get('parent_id');

        $subcategories = DocumentaryCategory::where('business_id', $business_id)
            ->where('parent_id', $parent_id)
            ->pluck('name', 'id');

        return response()->json($subcategories);
    }

    public function showQrcodeUrl($id)
    {

        $url = route('Documentary.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id)
    {
        $checkboxes = [

            [
                'id' => 'categorycontent',
                'label' => 'documentary::lang.category'
            ],
            [
                'id' => 'qrcontent',
                'label' => 'documentary::lang.qrcode'
            ],
            [
                'id' => 'createdbycontent',
                'label' => 'documentary::lang.createdby'
            ],
            [
                'id' => 'createdatcontent',
                'label' => 'documentary::lang.createdat'
            ],
            [
                'id' => 'title_1content',
                'label' => 'documentary::lang.title_1',
            ],
            [
                'id' => 'url_5content',
                'label' => 'documentary::lang.url_5',
            ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link = route('Documentary.qrcodeView', ['id' => $id]);
        $documentary = Documentary::findOrFail($id);
        $createdby = User::findOrFail($documentary->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('documentary::Documentary.qr_view')->with(compact('documentary', 'qrcode', 'link', 'checkboxes', 'name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch all categories to allow any category to be selected as a parent
        $main_categories = DocumentaryCategory::where('business_id', $business_id)->pluck('name', 'id');

        $documentary_categories = DocumentaryCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('documentary::Documentary.create')->with(compact(
            'main_categories',
            'documentary_categories',
            'leads',
            'users',
            'customer',
            'supplier',
            'product',
            'business_locations',
            'departments',
            'designations'
        ));
    }



    public function show($id, Request $request)
    {

        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('Documentary.qrcodeView', ['id' => $id]);

        $documentary = Documentary::where('business_id', $business_id)->findOrFail($id);
        $documentary = Documentary::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $documentary->getAttributes();

        // Find the first field that ends with '1'
        $first_field = null;
        foreach ($attributes as $fieldName => $fieldValue) {
            if (str_ends_with($fieldName, '1')) {
                $first_field = $fieldValue;
                break; // Get the first one found
            }
        }

        // Or get all fields that end with '1'
        $fields_ending_with_1 = [];
        foreach ($attributes as $fieldName => $fieldValue) {
            if (str_ends_with($fieldName, '1')) {
                $fields_ending_with_1[$fieldName] = $fieldValue;
            }
        }


        $createdby = User::findOrFail($documentary->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('documentary::Documentary.show')->with(compact('documentary', 'name', 'print_by', 'date_range', 'first_field'));
    }

    public function store(Request $request)
    {
        $filename = $request->input('filename');
        $index = $request->input('index');
        $totalChunks = $request->input('totalChunks');
        $chunk = $request->file('chunk');

        if ($chunk && $filename !== null) {
            $tempDir = storage_path("app/temp_uploads/{$filename}");
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $chunk->move($tempDir, "chunk_{$index}");

            if (count(glob("$tempDir/chunk_*")) == $totalChunks) {
                $finalPath = public_path("uploads/Documentary");
                if (!file_exists($finalPath)) {
                    mkdir($finalPath, 0755, true);
                }

                $finalFile = $finalPath . '/' . time() . '_' . preg_replace('/\s+/', '_', $filename);
                $output = fopen($finalFile, 'wb');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkPath = "$tempDir/chunk_{$i}";
                    $input = fopen($chunkPath, 'rb');
                    stream_copy_to_stream($input, $output);
                    fclose($input);
                    unlink($chunkPath);
                }

                fclose($output);
                rmdir($tempDir);

                // Store filename in session for later use
                session()->put('uploaded_file_path', 'uploads/Documentary/' . basename($finalFile));
            }

            return response()->json(['status' => 'chunk received']);
        }

        // Final save after upload
        $request->validate([
            'documentary_category_id' => 'nullable|integer',
            'title_1' => 'nullable|string',
            'url_5' => 'nullable|string',
        ]);

        try {
            $documentary = new Documentary();
            $documentary->business_id = request()->session()->get('user.business_id');
            $documentary->category_id = $request->input('documentary_category_id');
            $documentary->created_by = auth()->user()->id;
            $documentary->title_1 = $request->input('title_1');
            $documentary->url_5 = $request->input('url_5');
            $documentary->file_6 = session()->pull('uploaded_file_path'); // get and forget

            $documentary->save();

            // Optional Telegram notification
            $DocumentarySocial = DocumentarySocial::where('business_id', $documentary->business_id)->first();
            if ($DocumentarySocial && $DocumentarySocial->social_status == 1) {
                Http::post("https://api.telegram.org/bot{$DocumentarySocial->social_token}/sendMessage", [
                    'chat_id' => $DocumentarySocial->social_id,
                    'text' => __('documentary::lang.documentary_created'),
                ]);
            }

            return response()->json(['success' => true, 'msg' => __('documentary::lang.saved_successfully')]);
        } catch (\Exception $e) {
            \Log::error('Documentary Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type');
        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $documentary = Documentary::find($id);

        // Determine selected main category based on subcategory
        $selected_subcategory = DocumentaryCategory::find($documentary->category_id);
        $selected_main_category_id = $selected_subcategory ? $selected_subcategory->parent_id : null;

        $main_categories = DocumentaryCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $subcategories = DocumentaryCategory::where('business_id', $business_id)
            ->where('parent_id', $selected_main_category_id)
            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('documentary::Documentary.edit', compact(
            'documentary',
            'main_categories',
            'subcategories',
            'selected_main_category_id',
            'users',
            'customer',
            'supplier',
            'product',
            'business_locations',
            'departments',
            'designations',
            'leads'
        ));
    }
    public function update(Request $request, $id)
    {
        $filename = $request->input('filename');
        $index = $request->input('index');
        $totalChunks = $request->input('totalChunks');
        $chunk = $request->file('chunk');

        if ($chunk && $filename !== null) {
            $tempDir = storage_path("app/temp_uploads/{$filename}");
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $chunk->move($tempDir, "chunk_{$index}");

            if (count(glob("$tempDir/chunk_*")) == $totalChunks) {
                $finalPath = public_path("uploads/Documentary");
                if (!file_exists($finalPath)) {
                    mkdir($finalPath, 0755, true);
                }

                $finalFile = $finalPath . '/' . time() . '_' . preg_replace('/\s+/', '_', $filename);
                $output = fopen($finalFile, 'wb');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkPath = "$tempDir/chunk_{$i}";
                    $input = fopen($chunkPath, 'rb');
                    stream_copy_to_stream($input, $output);
                    fclose($input);
                    unlink($chunkPath);
                }

                fclose($output);
                rmdir($tempDir);

                session()->put('uploaded_file_path', 'uploads/Documentary/' . basename($finalFile));
            }

            return response()->json(['status' => 'chunk received']);
        }

        // Final save after chunked upload
        $request->validate([
            'documentary_category_id' => 'nullable|integer',
            'title_1' => 'nullable|string',
            'url_5' => 'nullable|string',
        ]);

        try {
            $documentary = Documentary::findOrFail($id);
            $business_id = request()->session()->get('user.business_id');

            if ($request->filled('documentary_category_id')) {
                $validCategory = DocumentaryCategory::where('business_id', $business_id)
                    ->where('id', $request->documentary_category_id)
                    ->exists();

                if (!$validCategory) {
                    return response()->json(['success' => false, 'msg' => __('documentary::lang.invalid_category')]);
                }

                $documentary->category_id = $request->documentary_category_id;
            }

            $documentary->created_by = auth()->user()->id;
            $documentary->title_1 = $request->title_1;
            $documentary->url_5 = $request->url_5;

            // Handle uploaded file from session
            if (session()->has('uploaded_file_path')) {
                if (!empty($documentary->file_6)) {
                    $oldFile = public_path($documentary->file_6);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $documentary->file_6 = session()->pull('uploaded_file_path');
            } elseif ($request->input('remove_file_6') == 1) {
                if (!empty($documentary->file_6)) {
                    $oldFile = public_path($documentary->file_6);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $documentary->file_6 = null;
            }

            $documentary->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.updated_successfully')]);
        } catch (\Exception $e) {
            \Log::error('Documentary Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroy($id)
    {
        try {
            Documentary::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentary::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $categories = DocumentaryCategory::with('parent') // eager load parent category
                ->where('business_id', $business_id)
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($categories)
                ->addColumn('parent_name', function ($row) {
                    return $row->parent ? $row->parent->name : '-';
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $filePath = 'uploads/DocumentaryCategory/' . basename($row->image);
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

                        if (in_array($ext, $imageExtensions)) {
                            return '<img src="' . asset($filePath) . '" style="max-height: 50px;">';
                        } elseif ($ext === 'pdf') {
                            return '<a href="' . asset($filePath) . '" target="_blank"><i class="fas fa-file-pdf" style="font-size: 20px; color: #dc3545;"></i></a>';
                        }
                    }
                    return '';
                })
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Documentary-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('Documentary-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('documentary::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all categories to allow any category to be selected as a parent
        $categories = DocumentaryCategory::where('business_id', $business_id)->get();

        return view('documentary::Category.create', compact('categories'));
    }


    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new DocumentaryCategory();
            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? null;

            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'DocumentaryCategory');
                $category->image = $documentPath;
            }

            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.saved_successfully')]);
        } catch (\Exception $e) {
            \Log::error('Documentary Category Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }


    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentary')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = DocumentaryCategory::findOrFail($id);

        // Fetch all categories except the current one to prevent circular reference
        $categories = DocumentaryCategory::where('business_id', $business_id)
            ->where('id', '!=', $id)
            ->get();

        return view('documentary::Category.edit', compact('category', 'categories'));
    }
    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = DocumentaryCategory::findOrFail($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            $category->parent_id = $request->parent_id ?? null;
            $category->description = $request->description;

            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/DocumentaryCategory/' . basename($category->image));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }

                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'DocumentaryCategory');
                $category->image = $documentPath;
            }

            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.updated_successfully')]);
        } catch (\Exception $e) {
            \Log::error('Documentary Category Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            DocumentaryCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentary::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}