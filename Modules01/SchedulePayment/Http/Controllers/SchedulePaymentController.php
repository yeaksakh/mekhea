<?php

namespace Modules\SchedulePayment\Http\Controllers;

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
use Modules\SchedulePayment\Entities\SchedulePayment;
use Modules\SchedulePayment\Entities\SchedulePaymentCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\SchedulePayment\Entities\SchedulePaymentSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class SchedulePaymentController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_schedulepayment = SchedulePayment::where('business_id', $business_id)->count();

        $total_schedulepayment_category =SchedulePaymentCategory::where('business_id', $business_id)->count();

        $schedulepayment_category = DB::table('schedulepayment_main as schedulepayment')
            ->leftJoin('schedulepayment_category as schedulepaymentcategory', 'schedulepayment.category_id', '=', 'schedulepaymentcategory.id')
            ->select(
                DB::raw('COUNT(schedulepayment.id) as total'),
                'schedulepaymentcategory.name as category'
            )
            ->where('schedulepayment.business_id', $business_id)
            ->groupBy('schedulepaymentcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('schedulepayment::SchedulePayment.dashboard')
            ->with(compact('total_schedulepayment', 'total_schedulepayment_category', 'schedulepayment_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $SchedulePayment = SchedulePayment::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $SchedulePayment->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $SchedulePayment->where('category_id', ${'category_id'});

            }

            $SchedulePayment->get();

            return DataTables::of($SchedulePayment)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SchedulePayment.show', $row->id) . '" data-container="#SchedulePayment_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SchedulePayment.edit', $row->id) . '" data-container="#SchedulePayment_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-SchedulePayment" data-href="' . route('SchedulePayment.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = SchedulePaymentCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                ->addColumn('status_7', function ($row) {
                                    return $row->{'status_7'} == 1 ? __('schedulepayment::lang.yes') : __('schedulepayment::lang.no');
                                })
                            

                                ->addColumn('note_8', function ($row) {
                                    return strip_tags($row->note_8);
                                })
                            
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = SchedulePaymentCategory::forDropdown($business_id);
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

        return view('schedulepayment::SchedulePayment.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('SchedulePayment.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'schedulepayment::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'schedulepayment::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'schedulepayment::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'schedulepayment::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'schedulepayment::lang.title_1',
                        ],
[
                            'id' => 'date_paid_5content',
                            'label' => 'schedulepayment::lang.date_paid_5',
                        ],
[
                            'id' => 'date_prepare_pay_6content',
                            'label' => 'schedulepayment::lang.date_prepare_pay_6',
                        ],
[
                            'id' => 'status_7content',
                            'label' => 'schedulepayment::lang.status_7',
                        ],
[
                            'id' => 'note_8content',
                            'label' => 'schedulepayment::lang.note_8',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('SchedulePayment.qrcodeView', ['id' => $id]);
        $schedulepayment = SchedulePayment::findOrFail($id);
        $createdby = User::findOrFail($schedulepayment->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('schedulepayment::SchedulePayment.qr_view')->with(compact('schedulepayment','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $schedulepayment_categories = SchedulePaymentCategory::forDropdown($business_id);
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

        return view('schedulepayment::SchedulePayment.create', compact('schedulepayment_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('SchedulePayment.qrcodeView', ['id' => $id]);

        $schedulepayment = SchedulePayment::where('business_id', $business_id)->findOrFail($id);
        $schedulepayment = SchedulePayment::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $schedulepayment->getAttributes();

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
        
        
        $createdby = User::findOrFail($schedulepayment->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('schedulepayment::SchedulePayment.show')->with(compact('schedulepayment','name','print_by', 'date_range','first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedulepayment_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'date_paid_5' => 'nullable',
                        

                            'date_prepare_pay_6' => 'nullable',
                        

                            'status_7' => 'nullable',
                        

                            'note_8' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $schedulepayment = new SchedulePayment();
            $schedulepayment->business_id = $business_id;
            $schedulepayment->category_id = $request->schedulepayment_category_id;
            $schedulepayment->created_by = auth()->user()->id;
            $SchedulePaymentSocial = SchedulePaymentSocial::where('business_id', $business_id)->first();
    
            if ($SchedulePaymentSocial && $SchedulePaymentSocial->social_status == 1) {
                $BotToken = $SchedulePaymentSocial->social_token;
                $ChatId = $SchedulePaymentSocial->social_id;
                $message = __('schedulepayment::lang.schedulepayment_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $schedulepayment->{'title_1'} = $request->{'title_1'};
                        

                            $schedulepayment->{'date_paid_5'} = $request->{'date_paid_5'};
                        

                            $schedulepayment->{'date_prepare_pay_6'} = $request->{'date_prepare_pay_6'};
                        

                            $schedulepayment->{'status_7'} = $request->{'status_7'};
                        

                            $schedulepayment->{'note_8'} = $request->{'note_8'};
                         
            
            $schedulepayment->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $schedulepayment = SchedulePayment::find($id);
        $schedulepayment_categories = SchedulePaymentCategory::forDropdown($business_id);
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
        return view('schedulepayment::SchedulePayment.edit', compact('schedulepayment', 'schedulepayment_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'schedulepayment_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'date_paid_5' => 'nullable',
                        

                            'date_prepare_pay_6' => 'nullable',
                        

                            'status_7' => 'nullable',
                        

                            'note_8' => 'nullable',
                                        
        ]);

        try {
            $schedulepayment = SchedulePayment::find($id);
            $schedulepayment->category_id = $request->schedulepayment_category_id;
            $schedulepayment->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $schedulepayment->{'title_1'} = $request->{'title_1'};
                        

                            $schedulepayment->{'date_paid_5'} = $request->{'date_paid_5'};
                        

                            $schedulepayment->{'date_prepare_pay_6'} = $request->{'date_prepare_pay_6'};
                        

                            $schedulepayment->{'status_7'} = $request->{'status_7'};
                        

                            $schedulepayment->{'note_8'} = $request->{'note_8'};
                         
            

            $schedulepayment->save();


            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            SchedulePayment::destroy($id);
            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = SchedulePaymentCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SchedulePayment-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('SchedulePayment-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('schedulepayment::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('schedulepayment::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new SchedulePaymentCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SchedulePaymentCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = SchedulePaymentCategory::find($id);
        return view('schedulepayment::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = SchedulePaymentCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SchedulePaymentCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            SchedulePaymentCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}