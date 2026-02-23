<?php

namespace Modules\ProductBook\Http\Controllers;

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
use Modules\ProductBook\Entities\ProductBook;
use Modules\ProductBook\Entities\ProductBookCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\ProductBook\Entities\ProductBookSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class ProductBookController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    )
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $total_productbook = ProductBook::where('business_id', $business_id)->count();

        $total_productbook_category =ProductBookCategory::where('business_id', $business_id)->count();

        $productbook_category = DB::table('productbook_main as productbook')
            ->leftJoin('productbook_category as productbookcategory', 'productbook.category_id', '=', 'productbookcategory.id')
            ->select(
                DB::raw('COUNT(productbook.id) as total'),
                'productbookcategory.name as category'
            )
            ->where('productbook.business_id', $business_id)
            ->groupBy('productbookcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('productbook::ProductBook.dashboard')
            ->with(compact('total_productbook', 'total_productbook_category', 'productbook_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $ProductBook = ProductBook::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $ProductBook->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $ProductBook->where('category_id', ${'category_id'});

            }

            $ProductBook->get();

            return DataTables::of($ProductBook)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductBook.show', $row->id) . '" data-container="#ProductBook_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductBook.edit', $row->id) . '" data-container="#ProductBook_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-ProductBook" data-href="' . route('ProductBook.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = ProductBookCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                ->addColumn('description_5', function ($row) {
                                    return strip_tags($row->description_5);
                                })
                            
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = ProductBookCategory::forDropdown($business_id);
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

        return view('productbook::ProductBook.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('ProductBook.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'productbook::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'productbook::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'productbook::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'productbook::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'productbook::lang.title_1',
                        ],
[
                            'id' => 'description_5content',
                            'label' => 'productbook::lang.description_5',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('ProductBook.qrcodeView', ['id' => $id]);
        $productbook = ProductBook::findOrFail($id);
        $createdby = User::findOrFail($productbook->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('productbook::ProductBook.qr_view')->with(compact('productbook','qrcode','link','checkboxes','name'));
    }

    /**
     * Standalone page focusing only on three tabs: passport_page1, passport_page2, package_franchise
     */
    public function threeTabs(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $view_type = $request->get('view', 'inner_page');
        // Default placeholder image if needed by embedded card templates
        $img_src = 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png';

        return view('productbook::customer.three_tabs')
            ->with(compact('view_type', 'img_src'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $productbook_categories = ProductBookCategory::forDropdown($business_id);
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

        return view('productbook::ProductBook.create', compact('productbook_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('ProductBook.qrcodeView', ['id' => $id]);

        $productbook = ProductBook::where('business_id', $business_id)->findOrFail($id);
        $productbook = ProductBook::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $productbook->getAttributes();

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
        
        
        $createdby = User::findOrFail($productbook->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('productbook::ProductBook.show')->with(compact('productbook','name','print_by', 'date_range','first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productbook_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'description_5' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $productbook = new ProductBook();
            $productbook->business_id = $business_id;
            $productbook->category_id = $request->productbook_category_id;
            $productbook->created_by = auth()->user()->id;
            $ProductBookSocial = ProductBookSocial::where('business_id', $business_id)->first();
    
            if ($ProductBookSocial && $ProductBookSocial->social_status == 1) {
                $BotToken = $ProductBookSocial->social_token;
                $ChatId = $ProductBookSocial->social_id;
                $message = __('productbook::lang.productbook_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $productbook->{'title_1'} = $request->{'title_1'};
                        

                            $productbook->{'description_5'} = $request->{'description_5'};
                         
            
            $productbook->save();

            return response()->json(['success' => true, 'msg' => __('productbook::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        

        $productbook = ProductBook::find($id);
        $productbook_categories = ProductBookCategory::forDropdown($business_id);
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
        return view('productbook::ProductBook.edit', compact('productbook', 'productbook_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'productbook_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'description_5' => 'nullable',
                                        
        ]);

        try {
            $productbook = ProductBook::find($id);
            $productbook->category_id = $request->productbook_category_id;
            $productbook->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $productbook->{'title_1'} = $request->{'title_1'};
                        

                            $productbook->{'description_5'} = $request->{'description_5'};
                         
            

            $productbook->save();


            return response()->json(['success' => true, 'msg' => __('productbook::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            ProductBook::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productbook::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            $categories = ProductBookCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductBook-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('ProductBook-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('productbook::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('productbook::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new ProductBookCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ProductBookCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productbook::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productbook')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.productbook')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }
            
        $category = ProductBookCategory::find($id);
        return view('productbook::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = ProductBookCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ProductBookCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productbook::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            ProductBookCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productbook::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}