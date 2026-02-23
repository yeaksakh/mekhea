<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\TaxRate;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Modules\Crm\Utils\CrmUtil;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderRequestController extends ApiController
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $commonUtil;
    protected $productUtil;
    protected $contactUtil;
    protected $crmUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil, Util $commonUtil, ProductUtil $productUtil, ContactUtil $contactUtil, CrmUtil $crmUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->contactUtil = $contactUtil;
        $this->crmUtil = $crmUtil;
        $this->moduleUtil = $moduleUtil;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $customer = Contact::where('business_id', auth()->user()->business_id)
            ->findOrFail(auth()->user()->crm_contact_id);

        $sells = $this->transactionUtil->getListSells($business_id, 'sales_order');

        $sells->where('transactions.contact_id', $customer->id)
            ->where('transactions.crm_is_order_request', 1)
            ->where('transactions.created_by', auth()->user()->id)
            ->groupBy('transactions.id');

        if ($request->has('location_id')) {
            $location_id = $request->get('location_id');
            if (!empty($location_id)) {
                $sells->where('transactions.location_id', $location_id);
            }
        }

        if (empty($request->startdate) || empty($request->enddate)) {
            $currentYear = Carbon::now()->year;
            $start = "{$currentYear}-01-01";
            $end = "{$currentYear}-12-31";
        } else {
            $start = $request->startdate;
            $end = $request->enddate;
        }

        $sells->whereDate('transactions.transaction_date', '>=', $start)
            ->whereDate('transactions.transaction_date', '<=', $end);

        if (!empty($request->input('status'))) {
            $sells->where('transactions.status', $request->input('status'));
        }

        $data = $sells->get()->map(function ($sell) {
            return [
                'id' => $sell->id,
                'final_total' => $sell->final_total,
                'contact_name' => $sell->supplier_business_name ? $sell->supplier_business_name . ', ' . $sell->name : $sell->name,
                'total_items' => $sell->total_items,
                'status' => $sell->status,
                'transaction_date' => $sell->transaction_date,
                'so_qty_remaining' => $sell->so_qty_remaining,
                'business_id' => $sell->business_location,
                'mobile' => $sell->phone_number,
                'invoice_no' => $sell->invoice_no,
                'staff_note' => $sell->staff_note,
                'additional_notes' => $sell->additional_notes,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
    
        $taxes = TaxRate::where('business_id', $business_id)->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with([
                'contact', 'delivery_person_user',
                'sell_lines' => function ($q) {
                    $q->whereNull('parent_sell_line_id');
                },
                'sell_lines.product', 'sell_lines.product.unit',
                'sell_lines.product.second_unit', 'sell_lines.variations',
                'sell_lines.variations.product_variation', 'payment_lines',
                'sell_lines.modifiers', 'sell_lines.lot_details', 'tax',
                'sell_lines.sub_unit', 'table', 'service_staff',
                'sell_lines.service_staff', 'types_of_service',
                'sell_lines.warranties', 'media'
            ]);
    
        if (
            !auth()->user()->can('sell.view') &&
            !auth()->user()->can('direct_sell.access') &&
            auth()->user()->can('view_own_sell_only')
        ) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }
    
        $sell = $query->firstOrFail();
    
        $activities = DB::table('activity_log')
            ->where('subject_id', $sell->id)
            ->where('subject_type', 'App\Transaction')
            ->get();
    
        $business_location = BusinessLocation::findOrFail($sell->location_id);

        $business_name = $business_location->name;
        $business_mobile = $business_location->mobile;

        $transaction = Transaction::where('business_id', $business_id)->findOrFail($id);
    
        $line_taxes = [];
        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
    
            if (!empty($taxes[$value->tax_id])) {
                if (isset($line_taxes[$taxes[$value->tax_id]])) {
                    $line_taxes[$taxes[$value->tax_id]] += ($value->item_tax * $value->quantity);
                } else {
                    $line_taxes[$taxes[$value->tax_id]] = ($value->item_tax * $value->quantity);
                }
            }
        }
    
        $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
        $order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }
    
        $business_details = $this->businessUtil->getDetails($business_id);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
    
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
    
        $statuses = Transaction::sell_statuses();
    
        if ($sell->type == 'sales_order') {
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $statuses = array_merge($statuses, $sales_order_statuses);
        }
        $status_color_in_activity = Transaction::sales_order_statuses();
        $sales_orders = $sell->salesOrders();
    
        $responseData = [
            [
                'taxes' => $taxes,
                'sell' => $sell,
                'payment_types' => $payment_types,
                'order_taxes' => $order_taxes,
                'pos_settings' => $pos_settings,
                'shipping_statuses' => $shipping_statuses,
                'is_warranty_enabled' => $is_warranty_enabled,
                'activities' => $activities,
                'statuses' => $statuses,
                'status_color_in_activity' => $status_color_in_activity,
                'sales_orders' => $sales_orders,
                'line_taxes' => $line_taxes,
                'business_name' => $business_name,
                'mobile' => $business_mobile,
            ]
        ];
    
        return response()->json([
            'data' => $responseData,
        ]);
    }
    

    public function getSellList(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $crm_contact_id = $user->crm_contact_id;

        $sells = $this->transactionUtil->getListSells($business_id);

        $sells->where('contacts.id', auth()->user()->crm_contact_id);

        if (empty($request->startdate) || empty($request->enddate)) {
            $currentYear = Carbon::now()->year;
            $start = "{$currentYear}-01-01";
            $end = "{$currentYear}-12-31";
        } else {
            $start = $request->startdate;
            $end = $request->enddate;
        }

        $sells->whereDate('transactions.transaction_date', '>=', $start)
            ->whereDate('transactions.transaction_date', '<=', $end);

        if (!empty($request->input('payment_status')) && $request->input('payment_status') != 'overdue') {
            $sells->where('transactions.payment_status', $request->input('payment_status'));
        } elseif ($request->input('payment_status') == 'overdue') {
            $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                ->whereNotNull('transactions.pay_term_number')
                ->whereNotNull('transactions.pay_term_type')
                ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
        }

        $sells->groupBy('transactions.id');

        $data = $sells->get()->map(function ($sell) {
            return [
                'id' => $sell->id,
                'transaction_date' => $sell->transaction_date,
                'invoice_no' => $sell->invoice_no,
                'payment_status' => Transaction::getPaymentStatus($sell),
                'payment_methods' => $sell->method,
                'final_total' => $sell->final_total,
                'total_paid' => $sell->total_paid,
                'total_remaining' => $sell->final_total - $sell->total_paid,
                'return_due' => $sell->amount_return - $sell->return_paid,
                'shipping_status' => $sell->shipping_status,
                'total_items' => $sell->total_items,
                'types_of_service_name' => $sell->types_of_service_name,
                'service_custom_field_1' => $sell->service_custom_field_1,
                'added_by' => $sell->added_by,
                'additional_notes' => $sell->additional_notes,
                'staff_note' => $sell->staff_note,
                'shipping_details' => $sell->shipping_details,
                'table_name' => $sell->table_name,
                'waiter' => $sell->waiter,
                'business_id' => $sell->business_location,
                'mobile' => $sell->phone_number,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }


public function store(Request $request)
    {
        $is_direct_sale = true;

        try {
            $input = $request->except('_token');

            $input['status'] = 'ordered';
            $input['type'] = 'sales_order';
            $input['discount_amount'] = 0;

            $user = Auth::user();
            $business_id = $user->business_id;
            $user_id = $user->id;

            $contact = Contact::where('business_id', $business_id)
                ->findOrFail($user->crm_contact_id);

            if (!empty($input['products'])) {
            
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], null);

                $input['final_total'] = $invoice_total['final_total'];

                DB::beginTransaction();

                $input['transaction_date'] = Carbon::now();
                $input['is_direct_sale'] = 1;

                //Customer group details
                $contact_id = $contact->id;
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                //Set selling price group id
                $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

                //If default price group for the location exists
                $price_group_id = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;

                $input['selling_price_group_id'] = $price_group_id;

                $crm_settings = $this->crmUtil->getCrmSettings($business_id);
                $order_request_prefix = $crm_settings['order_request_prefix'] ?? null;

                // $ref_count = $this->productUtil->setAndGetReferenceCount('crm_order_request');

                // $input['invoice_no'] = $this->productUtil->generateReferenceNumber('crm_order_request', $ref_count, $business_id, $order_request_prefix);

                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

                $transaction->crm_is_order_request = 1;
                $transaction->save();

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

                $this->transactionUtil->activityLog($transaction, 'added');

                DB::commit();

                $output = ['success' => 1, 'msg' => __('lang_v1.added_success')];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $msg = trans('messages.something_went_wrong');

            $output = [
                'success' => 0,
                'msg' => $msg,
            ];
        }

        return response()->json($output);
    }

}