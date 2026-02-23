<?php

namespace Modules\Announcement\Http\Controllers;

use App\User;
use App\Audit; 
use App\Contact;
use App\Product;
use App\Business;
use App\Category; 
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\TransactionUtil;
use Modules\Crm\Utils\CrmUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

use Modules\Announcement\Entities\Announcement;
use Modules\Announcement\Entities\AnnouncementSocial;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Announcement\Entities\AnnouncementCategory;



class AnnouncementController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.announcement')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_announcement = Announcement::where('business_id', $business_id)->count();

        $total_announcement_category =AnnouncementCategory::where('business_id', $business_id)->count();

        $announcement_category = DB::table('announcement_main as announcement')
            ->leftJoin('announcement_category as announcementcategory', 'announcement.category_id', '=', 'announcementcategory.id')
            ->select(
                DB::raw('COUNT(announcement.id) as total'),
                'announcementcategory.name as category'
            )
            ->where('announcement.business_id', $business_id)
            ->groupBy('announcementcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('announcement::Announcement.dashboard')
            ->with(compact('total_announcement', 'total_announcement_category', 'announcement_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $Announcement = Announcement::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $Announcement->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $Announcement->where('category_id', ${'category_id'});

            }

            $Announcement->get();

            return DataTables::of($Announcement)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Announcement.show', $row->id) . '" data-container="#Announcement_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Announcement.edit', $row->id) . '" data-container="#Announcement_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-Announcement" data-href="' . route('Announcement.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = AnnouncementCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = AnnouncementCategory::forDropdown($business_id);
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

        return view('announcement::Announcement.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    

    

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $announcement_categories = AnnouncementCategory::forDropdown($business_id);
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

        return view('announcement::Announcement.create', compact('announcement_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'announcement::lang.category'],
            ['id' => 'createdbycontent',
            'label' => 'announcement::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'announcement::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'announcement::lang.title_1',
                        ],
[
                            'id' => 'description_2content',
                            'label' => 'announcement::lang.description_2',
                        ],
[
                            'id' => 'date_3content',
                            'label' => 'announcement::lang.date_3',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $business = Business::where('id', $business_id)->first();

        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $announcement = Announcement::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($announcement->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        $date_range = $request->query('date_range');

        return view('announcement::Announcement.show')->with(compact('announcement','checkboxes','name', 'business', 'print_by', 'date_range'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'announcement_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'description_2' => 'nullable',
                        

                            'date_3' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $announcement = new Announcement();
            $announcement->business_id = $business_id;
            $announcement->category_id = $request->announcement_category_id;
            $announcement->created_by = auth()->user()->id;
            $AnnouncementSocial = AnnouncementSocial::where('business_id', $business_id)->first();
    
            if ($AnnouncementSocial && $AnnouncementSocial->social_status == 1) {
                $BotToken = $AnnouncementSocial->social_token;
                $ChatId = $AnnouncementSocial->social_id;
                $message = __('announcement::lang.announcement_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $announcement->{'title_1'} = $request->{'title_1'};
                        

                            $announcement->{'description_2'} = $request->{'description_2'};
                        

                            $announcement->{'date_3'} = $request->{'date_3'};
                         
            
            $announcement->save();

            return response()->json(['success' => true, 'msg' => __('announcement::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        

        $announcement = Announcement::find($id);
        $announcement_categories = AnnouncementCategory::forDropdown($business_id);
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
        return view('announcement::Announcement.edit', compact('announcement', 'announcement_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'announcement_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'description_2' => 'nullable',
                        

                            'date_3' => 'nullable',
                                        
        ]);

        try {
            $announcement = Announcement::find($id);
            $announcement->category_id = $request->announcement_category_id;
            $announcement->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $announcement->{'title_1'} = $request->{'title_1'};
                        

                            $announcement->{'description_2'} = $request->{'description_2'};
                        

                            $announcement->{'date_3'} = $request->{'date_3'};
                         
            

            $announcement->save();


            return response()->json(['success' => true, 'msg' => __('announcement::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            Announcement::destroy($id);
            return response()->json(['success' => true, 'msg' => __('announcement::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            $categories = AnnouncementCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('Announcement-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('Announcement-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('announcement::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('announcement::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new AnnouncementCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AnnouncementCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('announcement::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'announcement')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.announcement')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }
            
        $category = AnnouncementCategory::find($id);
        return view('announcement::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = AnnouncementCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AnnouncementCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('announcement::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            AnnouncementCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('announcement::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}