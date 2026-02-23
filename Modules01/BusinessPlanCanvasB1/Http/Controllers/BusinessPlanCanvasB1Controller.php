<?php

namespace Modules\BusinessPlanCanvasB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use App\Audit; 
use App\BusinessLocation;
use Yajra\DataTables\Facades\DataTables;
use Modules\BusinessPlanCanvasB1\Entities\BusinessPlanCanvasB1;
use Modules\BusinessPlanCanvasB1\Entities\BusinessPlanCanvasB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;


class BusinessPlanCanvasB1Controller extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
    )
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_businessplancanvasb1 = BusinessPlanCanvasB1::where('business_id', $business_id)->count();

        $total_businessplancanvasb1_category =BusinessPlanCanvasB1Category::where('business_id', $business_id)->count();

        $businessplancanvasb1_category = DB::table('businessplancanvasb1_main as businessplancanvasb1')
            ->leftJoin('businessplancanvasb1_category as businessplancanvasb1category', 'businessplancanvasb1.category_id', '=', 'businessplancanvasb1category.id')
            ->select(
                DB::raw('COUNT(businessplancanvasb1.id) as total'),
                'businessplancanvasb1category.name as category'
            )
            ->where('businessplancanvasb1.business_id', $business_id)
            ->groupBy('businessplancanvasb1category.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('businessplancanvasb1::BusinessPlanCanvasB1.dashboard')
            ->with(compact('total_businessplancanvasb1', 'total_businessplancanvasb1_category', 'businessplancanvasb1_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $BusinessPlanCanvasB1 = BusinessPlanCanvasB1::where('business_id', $business_id);
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $BusinessPlanCanvasB1->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            

            $BusinessPlanCanvasB1->get();

            return DataTables::of($BusinessPlanCanvasB1)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BusinessPlanCanvasB1.show', $row->id) . '" data-container=".BusinessPlanCanvasB1_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BusinessPlanCanvasB1.edit', $row->id) . '" data-container=".BusinessPlanCanvasB1_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-BusinessPlanCanvasB1" data-href="' . route('BusinessPlanCanvasB1.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->addColumn('category', function ($row) {
                    $category = BusinessPlanCanvasB1Category::find($row->category_id);
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
        $customer = Contact::where('business_id', $business_id)
        ->where('type', 'customer')
        ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
        ->where('type', 'supplier')
        ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
        ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);

        return view('businessplancanvasb1::BusinessPlanCanvasB1.index')->with(compact('module', 'users','customer', 'product', 'supplier', 'business_locations'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $businessplancanvasb1_categories = BusinessPlanCanvasB1Category::forDropdown($business_id);
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

        return view('businessplancanvasb1::BusinessPlanCanvasB1.create', compact('businessplancanvasb1_categories', 'users', 'customer', 'supplier', 'product', 'business_locations'));
    }

     public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
       if ((! auth()->user()->can('module.businessplancanvasb1')) || ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $businessplancanvasb1 = BusinessPlanCanvasB1::where('business_id', $business_id)->findOrFail($id);
        // dd($businessplancanvasb1);

        return view('businessplancanvasb1::BusinessPlanCanvasB1.view_BusinessPlanCanvasB1')->with(compact('businessplancanvasb1'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'businessplancanvasb1_category_id' => 'nullable|integer',
            
            
            
            
            
            
                            'CustomerSegments_1' => 'nullable',
                        

                            'ValuePropositions_2' => 'nullable',
                        

                            'Channels_3' => 'nullable',
                        

                            'CustomerRelationships_4' => 'nullable',
                        

                            'ReveneuStreams_5' => 'nullable',
                        

                            'KeyResources_6' => 'nullable',
                        

                            'KeyActivities_7' => 'nullable',
                        

                            'KeyPartner_8' => 'nullable',
                        

                            'CostStructure_9' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $businessplancanvasb1 = new BusinessPlanCanvasB1();
            $businessplancanvasb1->title = $request->title;
            $businessplancanvasb1->description = $request->description;
            $businessplancanvasb1->business_id = $business_id;
            $businessplancanvasb1->category_id = $request->businessplancanvasb1_category_id;
            $businessplancanvasb1->created_by = auth()->user()->id;
            $businessplancanvasb1->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $businessplancanvasb1->CustomerSegments_1 = $request->CustomerSegments_1;
                        

                            $businessplancanvasb1->ValuePropositions_2 = $request->ValuePropositions_2;
                        

                            $businessplancanvasb1->Channels_3 = $request->Channels_3;
                        

                            $businessplancanvasb1->CustomerRelationships_4 = $request->CustomerRelationships_4;
                        

                            $businessplancanvasb1->ReveneuStreams_5 = $request->ReveneuStreams_5;
                        

                            $businessplancanvasb1->KeyResources_6 = $request->KeyResources_6;
                        

                            $businessplancanvasb1->KeyActivities_7 = $request->KeyActivities_7;
                        

                            $businessplancanvasb1->KeyPartner_8 = $request->KeyPartner_8;
                        

                            $businessplancanvasb1->CostStructure_9 = $request->CostStructure_9;
                         
            
            $businessplancanvasb1->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $businessplancanvasb1 = BusinessPlanCanvasB1::find($id);
        $businessplancanvasb1_categories = BusinessPlanCanvasB1Category::forDropdown($business_id);
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
        return view('businessplancanvasb1::BusinessPlanCanvasB1.edit', compact('businessplancanvasb1', 'businessplancanvasb1_categories', 'users', 'customer', 'supplier', 'product', 'business_locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'businessplancanvasb1_category_id' => 'nullable|integer',
            
            
            
              
              
            
                            'CustomerSegments_1' => 'nullable',
                        

                            'ValuePropositions_2' => 'nullable',
                        

                            'Channels_3' => 'nullable',
                        

                            'CustomerRelationships_4' => 'nullable',
                        

                            'ReveneuStreams_5' => 'nullable',
                        

                            'KeyResources_6' => 'nullable',
                        

                            'KeyActivities_7' => 'nullable',
                        

                            'KeyPartner_8' => 'nullable',
                        

                            'CostStructure_9' => 'nullable',
                                        
        ]);

        try {
            $businessplancanvasb1 = BusinessPlanCanvasB1::find($id);
            $businessplancanvasb1->title = $request->title;
            $businessplancanvasb1->description = $request->description;
            $businessplancanvasb1->category_id = $request->businessplancanvasb1_category_id;
            $businessplancanvasb1->created_by = auth()->user()->id;
            
            
            
            
              
            
                            $businessplancanvasb1->CustomerSegments_1 = $request->CustomerSegments_1;
                        

                            $businessplancanvasb1->ValuePropositions_2 = $request->ValuePropositions_2;
                        

                            $businessplancanvasb1->Channels_3 = $request->Channels_3;
                        

                            $businessplancanvasb1->CustomerRelationships_4 = $request->CustomerRelationships_4;
                        

                            $businessplancanvasb1->ReveneuStreams_5 = $request->ReveneuStreams_5;
                        

                            $businessplancanvasb1->KeyResources_6 = $request->KeyResources_6;
                        

                            $businessplancanvasb1->KeyActivities_7 = $request->KeyActivities_7;
                        

                            $businessplancanvasb1->KeyPartner_8 = $request->KeyPartner_8;
                        

                            $businessplancanvasb1->CostStructure_9 = $request->CostStructure_9;
                         
            

            $businessplancanvasb1->save();


            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            BusinessPlanCanvasB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = BusinessPlanCanvasB1Category::where('business_id', $business_id)->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BusinessPlanCanvasB1-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('BusinessPlanCanvasB1-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('businessplancanvasb1::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('businessplancanvasb1::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new BusinessPlanCanvasB1Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = BusinessPlanCanvasB1Category::find($id);
        return view('businessplancanvasb1::category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = BusinessPlanCanvasB1Category::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            BusinessPlanCanvasB1Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}