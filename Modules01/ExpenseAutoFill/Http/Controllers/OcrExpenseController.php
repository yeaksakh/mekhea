<?php

namespace Modules\ExpenseAutoFill\Http\Controllers;


use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Events\PurchaseCreatedOrModified;

// app/Http/Controllers/OcrPurchaseController.php
class OcrPurchaseController extends Controller
{
    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', ];
    }



    public function create($imageId)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        // Get OCR data based on imageId
        $ocrData = $this->getOcrDataByImageId($imageId);

        if (!$ocrData || empty($ocrData)) {
            return redirect()->back()->with('status', [
                'success' => 0,
                'msg' => 'No OCR data available for this image'
            ]);
        }

        // Transform OCR data to purchase format
        $prefilledData = $this->transformOcrToPurchaseData($ocrData);

        // Get all the same data as the original create method
        $taxes = TaxRate::where('business_id', $business_id)
            ->ExcludeForTaxGroup()
            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];


        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        // ... continue with the rest of the original create method code

        return view('purchase.create_from_ocr')
            ->with(compact(
                'taxes',
                'orderStatuses',
                'business_locations',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bl_attributes',
                'common_settings',
                'prefilledData'  // Add this
            ));
    }

    private function getOcrDataByImageId($imageId)
    {
        // Query your database to get OCR data based on image ID
        // This will depend on your database structure
        // Example:
        return DB::table('telegram_bot_images')
            ->where('id', $imageId)
            ->value('ocr_data');
    }

    private function transformOcrToPurchaseData($ocrData)
    {
        // Parse the OCR data (likely JSON) and transform it to match your purchase form structure
        $ocrArray = json_decode($ocrData, true);

        // Map OCR fields to purchase fields
        return [
            'supplier_name' => $ocrArray['supplier_name'] ?? null,
            'ref_no' => $ocrArray['ref_no'] ?? null,
            'transaction_date' => $ocrArray['transaction_date'] ?? date('Y-m-d'),
            'final_total' => $ocrArray['final_total'] ?? 0,
            'additional_notes' => $ocrArray['additional_notes'] ?? null,
            'items' => $this->transformOcrItemsToPurchaseLines($ocrArray['items'] ?? [])
        ];
    }

    private function transformOcrItemsToPurchaseLines($ocrItems)
    {
        // Transform OCR items to purchase line format
        $purchaseLines = [];

        foreach ($ocrItems as $item) {
            $purchaseLines[] = [
                'product_name' => $item['name'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit_price' => $item['price'] ?? 0,
                // Add other item fields as needed
            ];
        }

        return $purchaseLines;
    }

    public function store(Request $request)
    {
        // This method will redirect to the original PurchaseController::store()
        // after any necessary preprocessing
        return app(PurchaseController::class)->store($request);
    }
}
