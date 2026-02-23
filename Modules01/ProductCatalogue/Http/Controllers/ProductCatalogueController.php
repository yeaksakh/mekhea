<?php

namespace Modules\ProductCatalogue\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\Discount;
use App\Product;
use App\SellingPriceGroup;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\ManageUserController;
use Spatie\Activitylog\Models\Activity;
use App\Utils\ContactUtil;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Endroid\QrCode\QrCode;

class ProductCatalogueController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    protected $manageUserController;

    protected $contactUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, ManageUserController $manageUserController, ContactUtil $contactUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->manageUserController = $manageUserController;
        $this->contactUtil = $contactUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
public function showProduct($business_id, $id, $group_price_id = null)
{
    $product = Product::where('business_id', $business_id)
        ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty', 'media'])
        ->findOrFail($id);

    $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

    $allowed_group_prices = [];
    foreach ($price_groups as $key => $value) {
        // Remove the ID prefix if it exists (e.g., "3.តំណាងចែកចាយ" -> "តំណាងចែកចាយ")
        $cleaned_value = preg_replace('/^\d+\./', '', $value);
        $allowed_group_prices[$key] = $cleaned_value;
    }

    $group_price_details = [];
    foreach ($product->variations as $variation) {
        foreach ($variation->group_prices as $group_price) {
            $group_price_details[$variation->id][$group_price->price_group_id] = [
                'price' => $group_price->price_inc_tax,
                'price_type' => $group_price->price_type,
                'calculated_price' => $group_price->calculated_price
            ];
        }
    }

    $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

    $combo_variations = [];
    if ($product->type == 'combo') {
        $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $business_id);
    }

    // If group_price_id is provided, show specific group price; otherwise show all
    $selected_group_price_details = [];
    $has_selected_group_price = false;
    
    if ($group_price_id) {
        foreach ($group_price_details as $variation_id => $prices) {
            if (isset($prices[$group_price_id])) {
                $selected_group_price_details[$variation_id] = $prices[$group_price_id];
                $has_selected_group_price = true;
            }
        }
    }

    return view('productcatalogue::catalogue.partials.show_product')->with(compact(
        'product',
        'rack_details',
        'allowed_group_prices',
        'group_price_details',
        'selected_group_price_details',
        'combo_variations',
        'group_price_id',
        'has_selected_group_price'
    ));
}


    public function showEmployee($id)
    {
        try {
            // Get the user with their associated business relationship
            $user = User::with('business')->findOrFail($id);

            // Get the associated business through the user
            $business = $user->business;

            $business_id = $user->business_id;

            // If the user doesn't belong to a business, handle it gracefully
            if (!$business) {
                return redirect()->back()->with('error', 'The user does not belong to any business.');
            }


            // Get activities for the user
            $activities = Activity::forSubject($user)
                ->with(['causer', 'subject'])
                ->latest()
                ->get();

            $user_department = Category::find($user->essentials_department_id);
            $user_designstion = Category::find($user->essentials_designation_id);

            $business_location = BusinessLocation::where('business_id', $business_id)
                ->select('city', 'state', 'country', 'landmark', 'zip_code')
                ->first();

            // Prepare data to pass to the view
            $data = [
                'user' => $user,
                'business' => $business,
                'activities' => $activities,
                'user_department' => $user_department,
                'user_designstion' => $user_designstion,
                'business_location' => $business_location
            ];


            // Return the view with the data
            return view('productcatalogue::catalogue.partials.show_employee', $data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where user is not found
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            // Handle any other exceptions
            return redirect()->back()->with('error', 'An error occurred while fetching the employee details.');
        }
    }


    public function showCustomer($business_id, $id)
    {
        // Try to get business
        $business = Business::find($business_id);

        if (!$business) {
            return response()->view('errors.404', [], 404);
        }

        // Fetch contact data
        $contact = $this->contactUtil->getContactInfo($business_id, $id);

        if (!$contact) {
            return response()->view('errors.404', [], 404);
        }

        // Reward program check (only for logged-in users)
        $reward_enabled = false;
        if (auth()->check() && session()->has('business.enable_rp')) {
            $reward_enabled = (session('business.enable_rp') == 1 &&
                in_array($contact->type, ['customer', 'both']));
        }

        // Initialize data
        $contact_dropdown = [];
        $contact_view_tabs = [];
        $activities = collect();

        // Only load sensitive data if user is logged in AND belongs to this business
        if (auth()->check() && auth()->user()->business_id == $business_id) {
            $contact_dropdown = Contact::contactDropdown($business_id, false, false);
            $contact_view_tabs = $this->moduleUtil->getModuleData('get_contact_view_tabs');
            $activities = Activity::forSubject($contact)
                ->with(['causer', 'subject'])
                ->latest()
                ->get();
        }

        return view('productcatalogue::catalogue.partials.show_customer')
            ->with(compact(
                'business',
                'contact',
                'reward_enabled',
                'contact_dropdown',
                // 'view_type', 
                'contact_view_tabs',
                'activities'
            ));
    }

    // Add this method to your controller
    protected function getPublicBusinessId()
    {
        // Implement logic to get a default/public business ID
        // This could be:
        // 1. A config value
        // 2. The first business in your system
        // 3. A business marked as 'public' in your database

        // Example:
        return Business::where('is_public', true)->value('id') ?? 1;
    }



    public function index($business_id, $location_id)
    {
        $products = Product::where('business_id', $business_id)
            ->whereHas('product_locations', function ($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            })
            ->ProductForSales()
            ->with(['variations', 'variations.product_variation', 'category'])
            ->get()
            ->groupBy('category_id');
        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);

        $now = \Carbon::now()->toDateTimeString();
        $discounts = Discount::where('business_id', $business_id)
            ->where('location_id', $location_id)
            ->where('is_active', 1)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderBy('priority', 'desc')
            ->get();
        foreach ($discounts as $key => $value) {
            $discounts[$key]->discount_amount = $this->productUtil->num_f($value->discount_amount, false, $business);
        }

        $categories = Category::forDropdown($business_id, 'product');

        return view('productcatalogue::catalogue.index')->with(compact('products', 'business', 'discounts', 'business_location', 'categories'));
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($business_id, $id)
    {
        $product = Product::with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty'])->where('business_id', $business_id)
            ->findOrFail($id);

        $price_groups = SellingPriceGroup::where('business_id', $product->business_id)->active()->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            $allowed_group_prices[$key] = $value;
        }

        $group_price_details = [];
        $discounts = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }

            $discounts[$variation->id] = $this->productUtil->getProductDiscount($product, $product->business_id, request()->input('location_id'), false, null, $variation->id);
        }

        $combo_variations = [];
        if ($product->type == 'combo') {
            $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $product->business_id);
        }

        return view('productcatalogue::catalogue.show')->with(compact(
            'product',
            'allowed_group_prices',
            'group_price_details',
            'combo_variations',
            'discounts'
        ));
    }

    public function exportCustomerQrExcel(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'productcatalogue_module'))) {
            abort(403, 'Unauthorized action.');
        }

        // Get customers based on the selection or all customers
        $customerIds = $request->input('customer_id', []);
        $query = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'supplier', 'both']);

        if (!empty($customerIds)) {
            $query->whereIn('id', $customerIds);
        }

        $customers = $query->get();

        // Get customer names for filename
        if (count($customers) == 1) {
            $customer = $customers->first();
            $customerName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $customer->first_name . '_' . $customer->last_name);
        } else {
            $customerName = 'multiple_customers';
        }

        // Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers in the exact order required for import
        $headers = [
            'CONTACT TYPE',
            'PREFIX',
            'FIRST NAME',
            'MIDDLE NAME',
            'LAST NAME',
            'BUSINESS NAME',
            'CONTACT ID',
            'TAX NUMBER',
            'OPENING BALANCE',
            'PAY TERM',
            'PAY TERM PERIOD',
            'CREDIT LIMIT',
            'EMAIL',
            'MOBILE',
            'ALT. CONTACT NO.',
            'LANDLINE',
            'CITY',
            'STATE',
            'COUNTRY',
            'ADDRESS LINE 1',
            'ADDRESS LINE 2',
            'ZIP CODE',
            'DOB',
            'CUSTOM FIELD 1',
            'CUSTOM FIELD 2',
            'CUSTOM FIELD 3',
            'CUSTOM FIELD 4'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Style the header row
        $sheet->getStyle('A1:AA1')->getFont()->setBold(true);
        $sheet->getStyle('A1:AA1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDDDDDD');

        // Add customer data
        $row = 2;
        foreach ($customers as $customer) {
            // Convert contact type to numeric value
            $contactType = '';
            if ($customer->type == 'customer') {
                $contactType = '1';
            } elseif ($customer->type == 'supplier') {
                $contactType = '2';
            } elseif ($customer->type == 'both') {
                $contactType = '3';
            }

            // Format date of birth if it exists
            $dob = '';
            if ($customer->dob) {
                $dob = date('Y-m-d', strtotime($customer->dob));
            }

            // Add data to spreadsheet in the exact order required
            $sheet->setCellValue('A' . $row, $contactType); // CONTACT TYPE
            $sheet->setCellValue('B' . $row, $customer->prefix); // PREFIX
            $sheet->setCellValue('C' . $row, $customer->first_name); // FIRST NAME
            $sheet->setCellValue('D' . $row, $customer->middle_name); // MIDDLE NAME
            $sheet->setCellValue('E' . $row, $customer->last_name); // LAST NAME
            $sheet->setCellValue('F' . $row, $customer->supplier_business_name); // BUSINESS NAME
            $sheet->setCellValue('G' . $row, $customer->contact_id); // CONTACT ID
            $sheet->setCellValue('H' . $row, $customer->tax_number); // TAX NUMBER
            $sheet->setCellValue('I' . $row, $customer->balance); // OPENING BALANCE
            $sheet->setCellValue('J' . $row, $customer->pay_term_number); // PAY TERM
            $sheet->setCellValue('K' . $row, $customer->pay_term_type); // PAY TERM PERIOD
            $sheet->setCellValue('L' . $row, $customer->credit_limit); // CREDIT LIMIT
            $sheet->setCellValue('M' . $row, $customer->email); // EMAIL
            $sheet->setCellValue('N' . $row, $customer->mobile); // MOBILE
            $sheet->setCellValue('O' . $row, $customer->alternate_number); // ALT. CONTACT NO.
            $sheet->setCellValue('P' . $row, $customer->landline); // LANDLINE
            $sheet->setCellValue('Q' . $row, $customer->city); // CITY
            $sheet->setCellValue('R' . $row, $customer->state); // STATE
            $sheet->setCellValue('S' . $row, $customer->country); // COUNTRY
            $sheet->setCellValue('T' . $row, $customer->address_line_1); // ADDRESS LINE 1
            $sheet->setCellValue('U' . $row, $customer->address_line_2); // ADDRESS LINE 2
            $sheet->setCellValue('V' . $row, $customer->zip_code); // ZIP CODE
            $sheet->setCellValue('W' . $row, $dob); // DOB
            $sheet->setCellValue('X' . $row, $customer->custom_field1); // CUSTOM FIELD 1
            $sheet->setCellValue('Y' . $row, $customer->custom_field2); // CUSTOM FIELD 2
            $sheet->setCellValue('Z' . $row, $customer->custom_field3); // CUSTOM FIELD 3
            $sheet->setCellValue('AA' . $row, $customer->custom_field4); // CUSTOM FIELD 4

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);

        // Create the Excel file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Set headers for download with customer name in filename
        $filename = $customerName . '_export_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function generateQr()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'productcatalogue_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business = Business::findOrFail($business_id);

        $employee = User::forDropdown($business_id);

      $customer = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'supplier', 'both'])
            ->get()
            ->mapWithKeys(function ($item) {
                // Create a display name with first_name + last_name and business name
                $name = trim($item->first_name . ' ' . $item->last_name);
                if (empty($name)) {
                    $name = $item->name; // Fallback to the name field if first and last name are empty
                }

                $displayName = $name;
                if (!empty($item->supplier_business_name)) {
                    $displayName .= ' - ' . $item->supplier_business_name;
                }

                return [$item->id => $displayName];
            })
            ->toArray();


        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');


        $group_prices = SellingPriceGroup::where('business_id', $business_id)
            ->pluck('name', 'id');


        $user_customer = User::where('business_id', $business_id)
            ->where('user_type', 'user_customer')
            ->pluck('username', 'id')
            ->all();


        return view('productcatalogue::catalogue.generate_qr')
            ->with(compact('business_locations', 'business', 'employee', 'customer', 'products', 'user_customer', 'business_id', 'group_prices'));
    }
}
