<?php

namespace Modules\News\Http\Controllers;

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
use Modules\News\Entities\News;
use Modules\News\Entities\NewsCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\News\Entities\NewsSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class NewsController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_news = News::where('business_id', $business_id)->count();

        $total_news_category =NewsCategory::where('business_id', $business_id)->count();

        $news_category = DB::table('news_main as news')
            ->leftJoin('news_category as newscategory', 'news.category_id', '=', 'newscategory.id')
            ->select(
                DB::raw('COUNT(news.id) as total'),
                'newscategory.name as category'
            )
            ->where('news.business_id', $business_id)
            ->groupBy('newscategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('news::News.dashboard')
            ->with(compact('total_news', 'total_news_category', 'news_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $News = News::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $News->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $News->where('category_id', ${'category_id'});

            }

            $News->get();

            return DataTables::of($News)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('News.show', $row->id) . '" data-container="#News_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('News.edit', $row->id) . '" data-container="#News_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-News" data-href="' . route('News.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = NewsCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                ->addColumn('description_6', function ($row) {
                                    return strip_tags($row->description_6);
                                })
                            
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = NewsCategory::forDropdown($business_id);
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

        return view('news::News.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('News.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'news::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'news::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'news::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'news::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'news::lang.title_1',
                        ],
[
                            'id' => 'description_6content',
                            'label' => 'news::lang.description_6',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('News.qrcodeView', ['id' => $id]);
        $news = News::findOrFail($id);
        $createdby = User::findOrFail($news->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('news::News.qr_view')->with(compact('news','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $news_categories = NewsCategory::forDropdown($business_id);
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

        return view('news::News.create', compact('news_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('News.qrcodeView', ['id' => $id]);

        $news = News::where('business_id', $business_id)->findOrFail($id);
        $news = News::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $news->getAttributes();

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
        
        
        $createdby = User::findOrFail($news->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('news::News.show')->with(compact('news','name','print_by', 'date_range','first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'news_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'description_6' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $news = new News();
            $news->business_id = $business_id;
            $news->category_id = $request->news_category_id;
            $news->created_by = auth()->user()->id;
            $NewsSocial = NewsSocial::where('business_id', $business_id)->first();
    
            if ($NewsSocial && $NewsSocial->social_status == 1) {
                $BotToken = $NewsSocial->social_token;
                $ChatId = $NewsSocial->social_id;
                $message = __('news::lang.news_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $news->{'title_1'} = $request->{'title_1'};
                        

                            $news->{'description_6'} = $request->{'description_6'};
                         
            
                            if ($request->hasFile('image_5')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_5', 'News');
                                $news->{'image_5'} = $documentPath;
                            }
                        

                            if ($request->hasFile('image_2_7')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_2_7', 'News');
                                $news->{'image_2_7'} = $documentPath;
                            }
                        
            $news->save();

            return response()->json(['success' => true, 'msg' => __('news::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $news = News::find($id);
        $news_categories = NewsCategory::forDropdown($business_id);
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
        return view('news::News.edit', compact('news', 'news_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'news_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'description_6' => 'nullable',
                                        
        ]);

        try {
            $news = News::find($id);
            $news->category_id = $request->news_category_id;
            $news->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $news->{'title_1'} = $request->{'title_1'};
                        

                            $news->{'description_6'} = $request->{'description_6'};
                         
            
                            if ($request->hasFile('image_5')) {
                                $oldFile = public_path('uploads/tracking/' . basename($news->{'image_5'}));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_5', 'News');
                                $news->{'image_5'} = $documentPath;
                            }
                        

                            if ($request->hasFile('image_2_7')) {
                                $oldFile = public_path('uploads/tracking/' . basename($news->{'image_2_7'}));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_2_7', 'News');
                                $news->{'image_2_7'} = $documentPath;
                            }
                        

            $news->save();


            return response()->json(['success' => true, 'msg' => __('news::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            News::destroy($id);
            return response()->json(['success' => true, 'msg' => __('news::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = NewsCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('News-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('News-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('news::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('news::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new NewsCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'NewsCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('news::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'news')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.news')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = NewsCategory::find($id);
        return view('news::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = NewsCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'NewsCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('news::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            NewsCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('news::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}