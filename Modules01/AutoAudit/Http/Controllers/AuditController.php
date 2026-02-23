<?php

namespace Modules\AutoAudit\Http\Controllers;

use App\User;
use App\Audit;
use App\Contact;
use App\Product;
use App\Category;
use App\Transaction;
use App\BusinessLocation;
use App\Http\Controllers\SellController;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\TransactionUtil;
use Modules\Crm\Utils\CrmUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Modules\AutoAudit\Entities\AutoAudit;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Modules\AutoAudit\Entities\AutoAuditSocial;
use Modules\AutoAudit\Entities\AutoAuditCategory;
use Modules\AutoAudit\Services\GoogleVisionService;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Carbon\Carbon;



class AuditController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $googleVisionService;
    protected $sells;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil,
        GoogleVisionService $googleVisionService,
        SellController $sells
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
        $this->businessUtil = $businessUtil;
        $this->productUtil = $productUtil;
        $this->googleVisionService = $googleVisionService;
        $this->sells = $sells;


         $this->dummyPaymentLine = [
            'method' => '',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => '',
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-blue',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
        $this->audit_status_colors = [
            'pending' => 'bg-yellow',
            'no_receipt' => 'bg-info',
            'confused' => 'bg-blue',
            'money' => 'bg-red',
            'done' => 'bg-green',
        ];
    }
    
     

    private function querySell($business_id)
    {
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $audit_statuses = $this->transactionUtil->audit_statuses();

        $sale_type = ! empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';

        $sells = $this->transactionUtil->getListSells($business_id, $sale_type);
        // Laravel query-builder / Eloquent
        // $sells->join('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id');
        // $sells->where('transactions.payment_status', 'paid')
        //     // ->where('transactions.audit_status', 'pending')
        //     ->where('transaction_payments.method', 'bank_transfer')
        //     ->where(function ($query) {
        //         $query->whereNotNull('transactions.document')
        //             ->orWhereHas('payment_lines', function ($q) {
        //                 $q->whereNotNull('document');
        //             });
        //     });

        return [$sells, $sale_type, $shipping_statuses, $audit_statuses, $payment_types];
    }

    public function botAudit()
    {
        session(['audit_type' => 'botaudit']);
        return $this->handleAudit();
    }

    public function botNotAudit()
    {
        session(['audit_type' => 'botnoaudit']);
        return $this->handleAudit();
    }

    public function index()
    {
        // On initial page load, set to default.
        // On AJAX, do nothing, preserving the session from botAudit/botNotAudit.
        if (!request()->ajax()) {
            session(['audit_type' => 'default']);
        }

        return $this->handleAudit();
    }

    public function handleAudit()
    {

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }


        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_crm = $this->moduleUtil->isModuleInstalled('Crm');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        //  dump($type);
          try {

            if (request()->ajax()) {


            [$sells, $sale_type, $shipping_statuses, $audit_statuses, $payment_types] = $this->querySell($business_id);


            // $type = "botnoaudit";
            $type = session('audit_type', 'default');

            // dd($type);

            // handle bot audited 
            if ($type == "botaudit") {
                $sells = $sells->join('autoaudit_main', 'autoaudit_main.transaction_id', '=', 'transactions.id')
                    ->where('transactions.audit_status', 'done')
                    ->where('autoaudit_main.audit_status_2', 'true');
            } elseif ($type == "botnoaudit") {
                $sells = $sells->join('autoaudit_main', 'autoaudit_main.transaction_id', '=', 'transactions.id')
                    ->where('transactions.audit_status', 'pending')
                    ->where('autoaudit_main.audit_status_2', 'false');
            } elseif ($type == "default") {
            $sells->join('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id')
                ->leftJoin('autoaudit_main', 'autoaudit_main.transaction_id', '=', 'transactions.id')
                ->whereNull('autoaudit_main.id')
                ->where('transactions.payment_status', 'paid')
                ->where('transactions.audit_status', 'pending')
                ->where('transaction_payments.method', 'bank_transfer')
                ->where(function ($query) {
                    $query->whereNotNull('transactions.document')
                            ->orWhereHas('payment_lines', function ($q) {
                                $q->whereNotNull('document');
                            });
                });
    }


            // only display sell invoice we add it because project invoive show in sell list
            if ($sale_type == 'sell') {
                $sells->whereNull('transactions.sub_type');
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments) {
                $sells->whereNotNull('transactions.shipping_status');

                if (auth()->user()->hasAnyPermission(['access_pending_shipments_only'])) {
                    $sells->where('transactions.shipping_status', '!=', 'delivered');
                }
            }

            if (! $is_admin && ! $only_shipments && $sale_type != 'sales_order') {
                $payment_status_arr = [];
                if (auth()->user()->can('view_paid_sells_only')) {
                    $payment_status_arr[] = 'paid';
                }

                if (auth()->user()->can('view_due_sells_only')) {
                    $payment_status_arr[] = 'due';
                }

                if (auth()->user()->can('view_partial_sells_only')) {
                    $payment_status_arr[] = 'partial';
                }

                if (empty($payment_status_arr)) {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->OverDue();
                    }
                } else {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->where(function ($q) use ($payment_status_arr) {
                            $q->whereIn('transactions.payment_status', $payment_status_arr)
                                ->orWhere(function ($qr) {
                                    $qr->OverDue();
                                });
                        });
                    } else {
                        $sells->whereIn('transactions.payment_status', $payment_status_arr);
                    }
                }
            }

            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (! empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
            }

            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;

                session(['start_date' => $start, 'end_date' => $end]);

                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            } else {
                //Clear session dates if not provided in request
                session()->forget(['start_date', 'end_date']);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (! empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if (! empty(request()->input('source'))) {
                //only exception for woocommerce
                if (request()->input('source') == 'woocommerce') {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                } else {
                    $sells->where('transactions.source', request()->input('source'));
                }
            }

            if ($is_crm) {
                $sells->addSelect('transactions.crm_is_order_request');

                if (request()->has('crm_is_order_request')) {
                    $sells->where('transactions.crm_is_order_request', 1);
                }
            }

            if (request()->only_subscriptions) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.recur_parent_id')
                        ->orWhere('transactions.is_recurring', 1);
                });
            }

            if (request()->only_francier) {
                $sells->whereIn('tsl.product_id', [3628, 3664, 3669, 3670]);
            }

            if (! empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
            }

            if (! empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }

            if (! empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (! empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }

            if (! empty(request()->input('status'))) {
                $sells->where('transactions.status', request()->input('status'));
            }

            if (! empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (! empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }

            $only_pending_shipments = request()->only_pending_shipments == 'true' ? true : false;
            if ($only_pending_shipments) {
                $sells->where('transactions.shipping_status', '!=', 'delivered')
                    ->whereNotNull('transactions.shipping_status');
                $only_shipments = true;
            }

            if (! empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            if (! empty(request()->input('for_dashboard_sales_order'))) {
                $sells->whereIn('transactions.status', ['partial', 'ordered'])
                    ->orHavingRaw('so_qty_remaining > 0');
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            if (! empty(request()->input('delivery_person'))) {
                $sells->where('transactions.delivery_person', request()->input('delivery_person'));
            }
            if (request()->has('audit_status')) {
                $status = request()->input('audit_status');
                if (!empty($status)) {
                    $sells->where('tsl.audit_status', $status);
                }
            }

            $sells->groupBy('transactions.id');

            if (! empty(request()->suspended)) {
                $transaction_sub_type = request()->get('transaction_sub_type');
                if (! empty($transaction_sub_type)) {
                    $sells->where('transactions.sub_type', $transaction_sub_type);
                } else {
                    $sells->where('transactions.sub_type', null);
                }

                $with = ['sell_lines'];

                if ($is_tables_enabled) {
                    $with[] = 'table';
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }

                $sales = $sells->where('transactions.is_suspend', 1)
                    ->with($with)
                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                    ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales', 'is_tables_enabled', 'is_service_staff_enabled', 'transaction_sub_type'));
            }

            $with[] = 'payment_lines';

            if (!empty($with)) {
                foreach ($with as $relation) {
                    if ($relation == 'payment_lines' && !empty(request()->input('payment_method'))) {
                        $sells->whereHas($relation, function ($query) {
                            $query->where('method', request()->input('payment_method'));
                        });
                    } else {
                        $sells->with($relation);
                    }
                }
            }

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            $sales_order_statuses = Transaction::sales_order_statuses();
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments, $is_admin, $sale_type) {
                        $html = '<div class="btn-group">
                                    <button type="button" 
                                class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-success tw-w-max dropdown-toggle green-border-btn" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __('') .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true" style="color: blue; border: 1px solid blue; padding: 5px; border-radius: 10px;"></i> ' . __('messages.view') . '</a></li>';
                        }
                        if (!$only_shipments) {
                            if ($row->is_direct_sale == 0) {
                                if (auth()->user()->can('sell.update')) {
                                    $html .= '<li><a target="_blank" href="' . action([\App\Http\Controllers\SellController::class, 'edit'], [$row->id]) . '"><i class="fas fa-edit" style="color: orange; border: 1px solid orange; padding: 5px; border-radius: 10px;"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            } elseif ($row->type == 'sales_order') {
                                if (auth()->user()->can('so.update')) {
                                    $html .= '<li><a target="_blank" href="' . action([\App\Http\Controllers\SellController::class, 'edit'], [$row->id]) . '"><i class="fas fa-edit" style="color: orange; border: 1px solid orange; padding: 5px; border-radius: 10px;"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            } else {
                                if (auth()->user()->can('direct_sell.update')) {
                                    $html .= '<li><a target="_blank" href="' . action([\App\Http\Controllers\SellPosController::class, 'edit'], [$row->id]) . '"><i class="fas fa-edit" style="color: orange; border: 1px solid orange; padding: 5px; border-radius: 10px;"></i> ' . __('messages.edit') . '</a></li>';
                                }
                            }

                            $delete_link = '<li><a href="' . action([\App\Http\Controllers\SellPosController::class, 'destroy'], [$row->id]) . '" class="delete-sale"><i class="fas fa-trash" style="color: red; border: 1px solid red; padding: 5px; border-radius: 10px;"></i> ' . __('messages.delete') . '</a></li>';
                            if ($row->is_direct_sale == 0) {
                                if (auth()->user()->can('sell.delete')) {
                                    $html .= $delete_link;
                                }
                            } elseif ($row->type == 'sales_order') {
                                if (auth()->user()->can('so.delete')) {
                                    $html .= $delete_link;
                                }
                            } else {
                                if (auth()->user()->can('direct_sell.delete')) {
                                    $html .= $delete_link;
                                }
                            }
                        }

                        if (config('constants.enable_download_pdf') && auth()->user()->can('print_invoice') && $sale_type != 'sales_order') {
                            $html .= '<li><a href="' . route('sell.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true" style="color: maroon; border: 1px solid maroon; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.download_pdf') . '</a></li>';

                            if (!empty($row->shipping_status)) {
                                $html .= '<li><a href="' . route('packing.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true" style="color: maroon; border: 1px solid maroon; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.download_paking_pdf') . '</a></li>';
                            }
                        }

                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.access')) {
                            if (!empty($row->document)) {
                                $document_name = !empty(explode('_', $row->document, 2)[1]) ? explode('_', $row->document, 2)[1] : $row->document;
                                $html .= '<li><a href="' . url('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i>' . __('purchase.download_document') . '</a></li>';
                                if (isFileImage($document_name)) {
                                    $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) . '" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i>' . __('lang_v1.view_document') . '</a></li>';
                                }
                            }
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'editShipping'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true" style="color: #279dff; border: 1px solid #279dff; padding: 5px; border-radius: 10px;"></i>' . __('lang_v1.edit_shipping') . '</a></li>';
                        }

                        if ($row->type == 'sell') {
                            if (auth()->user()->can('print_invoice')) {
                                $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true" style="color: #ff27f2; border: 1px solid #ff27f2; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.print_invoice') . '</a></li>
                                    <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-box-open" aria-hidden="true" style="color: #279dff; border: 1px solid #279dff; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.packing_slip') . '</a></li>';

                                $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?delivery_note=true"><i class="fas fa-car" aria-hidden="true" style="color: #279dff; border: 1px solid #279dff; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.delivery_note') . '</a></li>';
                            }
                            $html .= '<li class="divider"></li>';
                            if (!$only_shipments) {
                                if (
                                    $row->is_direct_sale == 0 && !auth()->user()->can('sell.update') &&
                                    auth()->user()->can('edit_pos_payment')
                                ) {
                                    $html .= '<li><a href="' . route('edit-pos-payment', [$row->id]) . '" 
                                    ><i class="fas fa-money-bill-alt" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.add_edit_payment') .
                                        '</a></li>';
                                }

                                if (
                                    auth()->user()->can('sell.payments') ||
                                    auth()->user()->can('edit_sell_payment') ||
                                    auth()->user()->can('delete_sell_payment')
                                ) {
                                    if ($row->payment_status != 'paid') {
                                        $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$row->id]) . '" class="add_payment_modal"><i class="fas fa-comment-dollar" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i> ' . __('purchase.add_payment') . '</a></li>';
                                    }

                                    $html .= '<li><a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->id]) . '" class="view_payment_modal"><i class="fas fa-comment-dollar" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i> ' . __('purchase.view_payments') . '</a></li>';
                                }

                                if (auth()->user()->can('sell.create') || auth()->user()->can('direct_sell.access')) {
                                    // $html .= '<li><a href="' . action([\App\Http\Controllers\SellController::class, 'duplicateSell'], [$row->id]) . '"><i class="fas fa-copy"></i> ' . __("lang_v1.duplicate_sell") . '</a></li>';

                                    $html .= '<li><a href="' . action([\App\Http\Controllers\SellReturnController::class, 'add'], [$row->id]) . '"><i class="fas fa-exchange-alt" style="color: #2362ca; border: 1px solid #2362ca; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.sell_return') . '</a></li>

                                    <li><a href="' . action([\App\Http\Controllers\SellPosController::class, 'showInvoiceUrl'], [$row->id]) . '" class="view_invoice_url"><i class="fas fa-link" style="color: #07d4e5; border: 1px solid #07d4e5; padding: 5px; border-radius: 10px;"></i> ' . __('lang_v1.view_invoice_url') . '</a></li>';
                                }
                            }

                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'new_sale']) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true" style="color: blue; border: 1px solid blue; padding: 5px; border-radius: 10px;"></i>' . __('lang_v1.new_sale_notification') . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'viewMedia'], ['model_id' => $row->id, 'model_type' => \App\Transaction::class, 'model_media_type' => 'shipping_document']) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true" style="color: green; border: 1px solid green; padding: 5px; border-radius: 10px;"></i></i>' . __('lang_v1.shipping_documents') . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="total-tax" data-orig-value="{{$tax_amount}}">@format_currency($tax_amount)</span>'
                )
                ->editColumn(
                    'commission_agent',
                    function ($row) {
                        // Check if commission_agent exists before querying
                        if (!empty($row->commission_agent)) {
                            $commission_agent = User::select('first_name', 'last_name')
                                ->where('id', $row->commission_agent)
                                ->first();

                            return $commission_agent
                                ? $commission_agent->first_name . ' ' . $commission_agent->last_name
                                : '';
                        }

                        return '';
                    }
                )
                ->editColumn(
                    'total_paid',
                    '<span class="total-paid" data-orig-value="{{$total_paid}}">@format_currency($total_paid)</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="total_before_tax" data-orig-value="{{$total_before_tax}}">@format_currency($total_before_tax)</span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="total-discount" data-orig-value="' . $discount . '">' . $this->transactionUtil->num_f($discount, true) . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);

                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                    }
                )
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining = $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' . $this->transactionUtil->num_f($total_remaining, true) . '</span>';

                    return $total_remaining_html;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';
                    if (! empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $return_due_html .= '<a href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="sell_return_due" data-orig-value="' . $return_due . '">' . $this->transactionUtil->num_f($return_due, true) . '</span></a>';
                    }

                    return $return_due_html;
                })
                ->editColumn('invoice_no', function ($row) use ($is_crm) {
                    $invoice_no = $row->invoice_no;
                    if (! empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (! empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fas fa-exchange-alt"></i></small>';
                    }
                    if (! empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (! empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (! empty($row->is_export)) {
                        $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
                    }

                    if ($is_crm && ! empty($row->crm_is_order_request)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-yellow label-round no-print" title="' . __('crm::lang.order_request') . '"><i class="fas fa-tasks"></i></small>';
                    }

                    return $invoice_no;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = ! empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = ! empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action([\App\Http\Controllers\SellController::class, 'editShipping'], [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

                    return $status;
                })
                ->editColumn('audit_status', function ($row) use ($audit_statuses) {

                    $status_color = ! empty($this->audit_status_colors[$row->audit_status]) ? $this->audit_status_colors[$row->audit_status] : 'bg-gray';
                    $status = ! empty($row->audit_status) ? '<a href="#" class="btn-modal" data-href="' . action([\App\Http\Controllers\SellController::class, 'editAudit'], [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $audit_statuses[$row->audit_status] . '</span></a>' : '';

                    return $status;
                })
                ->addColumn('conatct_name', function ($row) {
                    $contactName = '';
                    if (!empty($row->supplier_business_name)) {
                        $contactName .= $row->supplier_business_name . ', <br>';
                    }
                    $contactName .= $row->name;
                    return '<a style="color: black;" href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->contacts_id]) . '">' . $contactName . '</a>';
                })
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->addColumn('contact_group', function ($row) {
                    if ($row->customer_group) {
                        // Use proper string concatenation and avoid Blade syntax
                        return '<a href="' . route('customer-group.index') . '" style="color: black;">' . $row->customer_group . '</a>';
                    }
                    return 'N/A';
                })
                ->addColumn('added_by', function ($row) {
                    if (!empty($row->added_by_user_id)) {
                        return '<a href="' . route('users.show', $row->added_by_user_id) . '" style="color: black;">' . $row->added_by . '</a>';
                    }
                    return $row->added_by ?? '';
                })
                // ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]] ?? '';
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })
                ->editColumn('status', function ($row) use ($sales_order_statuses, $is_admin) {
                    $status = '';

                    if ($row->type == 'sales_order') {
                        if ($is_admin && $row->status != 'completed') {
                            $status = '<span class="edit-so-status label ' . $sales_order_statuses[$row->status]['class'] . '" data-href="' . action([\App\Http\Controllers\SalesOrderController::class, 'getEditSalesOrderStatus'], ['id' => $row->id]) . '">' . $sales_order_statuses[$row->status]['label'] . '</span>';
                        } else {
                            $status = '<span class="label ' . $sales_order_statuses[$row->status]['class'] . '" >' . $sales_order_statuses[$row->status]['label'] . '</span>';
                        }
                    }

                    return $status;
                })
                ->editColumn('so_qty_remaining', '{{@format_quantity($so_qty_remaining)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ]);

            $rawColumns = ['final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'audit_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'contact_group', 'status', 'added_by', 'commission_agent'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
            

        }catch (\Throwable $e) {
            \Log::error("autoAudit global error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'A critical error occurred: ' . $e->getMessage()], 500);
        }


        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (! empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $audit_statuses = $this->transactionUtil->audit_statuses();

        $sources = $this->transactionUtil->getSources($business_id);
        if ($is_woocommerce) {
            $sources['woocommerce'] = 'Woocommerce';
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);


        return view('autoaudit::AutoAudit.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses', 'audit_statuses', 'sources', 'payment_types'));
    }

    public function autoAudit()
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get date range from session, if available
            $start_date = session('start_date');
            $end_date = session('end_date');

            /* 1. Define the invoices we want to try ------------------------------ */
            $wantedIdQuery = $this->getInvoiceId($start_date, $end_date);

            // Exclude invoices already in the audit log for the given period
            $existingIdsQuery = AutoAudit::query();
            if (!empty($start_date) && !empty($end_date)) {
                $existingIdsQuery->whereBetween('created_at', [$start_date, $end_date]);
            }

            // $wantedIds = $wantedIdQuery->whereNotIn('transactions.id', $existingIds)->pluck('id');
            $wantedIds = $wantedIdQuery->pluck('id');

            // dump($wantedIds->isEmpty());


            if ($wantedIds->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No new invoices to audit in the selected date range.',
                    'results' => []
                ]);
            }

            /* 2. Grab invoice + document rows in one hit, de-duplicated ---------- */
            [$baseQuery] = $this->querySell($business_id);
            $rows = $baseQuery
                ->join('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id')
                ->select(
                    'transactions.id',
                    'transactions.final_total',
                    'transactions.transaction_date',
                    'transactions.document as transaction_document',
                    'transaction_payments.document as payment_document'
                )
                ->whereIn('transactions.id', $wantedIds)
                ->groupBy('transactions.id')
                ->get()
                ->keyBy('id');

            /* 3. Process every requested ID ------------------------------------- */
            $results = [];
            foreach ($wantedIds as $invoiceId) {
                if (!isset($rows[$invoiceId])) {
                    $results[] = ['invoice_id' => $invoiceId, 'success' => false, 'message' => 'Invoice not found or does not meet criteria'];
                    continue;
                }
                $row = $rows[$invoiceId];
                $docPath = $row->payment_document ?? $row->transaction_document;
                $fullPath = public_path('uploads/documents/' . $docPath);

                if (!$docPath || !file_exists($fullPath)) {
                    $results[] = ['invoice_id' => $invoiceId, 'success' => false, 'message' => 'Screenshot not found'];
                    break;
                }

                try {
                    $invoiceAmount = (float) $row->final_total;
                    $invoiceDate = \Carbon\Carbon::parse($row->transaction_date)->format('Y-m-d');
                    $base64Image = base64_encode(file_get_contents($fullPath));
                    $aiResponse = $this->googleVisionService->extractPaymentData($base64Image);

                    if (!$aiResponse['success']) {
                        throw new \RuntimeException($aiResponse['message']);
                    }
                    $paymentData = $aiResponse['data'];
                    $amountFound = isset($paymentData['amount']) ? abs((float) $paymentData['amount']) : null;
                    $accountFound = (isset($paymentData['receiver']) && stripos($paymentData['receiver'], 'LONG SYCHAN') !== false) || (isset($paymentData['sender']) && stripos($paymentData['sender'], 'LONG SYCHAN') !== false) ? 'longsychan' : null;
                    $dateFound = null;
                    if (!empty($paymentData['datetime']) || !empty($paymentData['date'])) {
                        $dateFound = \Carbon\Carbon::parse($paymentData['datetime'] ?? $paymentData['date'])->format('Y-m-d');
                    }

                    $audit = [
                        'invoice' => ['id' => $invoiceId, 'amount' => $invoiceAmount, 'date' => $invoiceDate],
                        'screenshot' => ['amount' => $amountFound, 'account' => $accountFound, 'date' => $dateFound],
                        'match' => ['amount' => $amountFound !== null && abs($amountFound) === abs($invoiceAmount), 'account' => $accountFound === 'longsychan'],
                        'success' => true,
                    ];

                    \DB::transaction(function () use ($business_id, $audit) {
                        $ok = $audit['match']['amount'] && $audit['match']['account'];
                        \DB::table('transactions')->where('id', $audit['invoice']['id'])->update(['audit_status' => $ok ? 'done' : 'pending']);
                        \DB::table('autoaudit_main')->insert([
                            'business_id' => $business_id,
                            'transaction_id' => (float) $audit['invoice']['id'],
                            'audit_status_2' => $ok ? 'true' : 'false',
                            'created_by' => auth()->id() ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });
                    $results[] = $audit;
                } catch (\Throwable $e) {
                    \Log::error("autoAudit error for invoice {$invoiceId}: " . $e->getMessage());
                    $results[] = ['invoice_id' => $invoiceId, 'success' => false, 'message' => 'Processing failed: ' . $e->getMessage()];
                    \DB::table('autoaudit_main')->insert([
                        'business_id' => $business_id,
                        'transaction_id' => (int) $invoiceId,
                        'audit_status_2' => 'false',
                        'created_by' => auth()->id() ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            return response()->json(['success' => true, 'message' => 'Audit completed successfully!', 'results' => $results]);
        } catch (\Throwable $e) {
            \Log::error("autoAudit global error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'A critical error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getInvoiceId($start_date, $end_date)
    {
        $business_id = request()->session()->get('user.business_id');
        [$sells] = $this->querySell($business_id);

        $query = $sells->join('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
            ->leftJoin('autoaudit_main', 'transactions.id', '=', 'autoaudit_main.transaction_id')
            ->select('transactions.id')
            ->whereNull('autoaudit_main.audit_status_2')
            ->where('transactions.audit_status', 'pending')
            ->where('transactions.payment_status', 'paid')
            ->where('transactions.type', 'sell')
            ->where('transactions.business_id', $business_id)
            ->whereNotNull('transaction_payments.document')
            ->where('transaction_payments.method', 'bank_transfer')
            ->orderBy('transactions.transaction_date', 'desc')
            ->groupBy('transactions.id');

        // Fix 1: Ensure date variables are defined and properly formatted
        if (!empty($start_date) && !empty($end_date)) {
            // Convert to proper date format if they're strings
            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');
            
            // Use whereDate for date-only comparison (ignores time)
            $query->whereDate('transactions.transaction_date', '>=', $start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        // Alternative approach for today only:
        // $query->whereDate('transactions.transaction_date', Carbon::today());

        $results = $query->get()->toArray();
        // dump($results);

        return $query;
    }

    public function getinvoices()
    {
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $audit_statuses = $this->transactionUtil->audit_statuses();

        $sources = $this->transactionUtil->getSources($business_id);
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        if ($is_woocommerce) {
            $sources['woocommerce'] = 'Woocommerce';
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        return view('autoaudit::AutoAudit.invoice')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses', 'audit_statuses', 'sources', 'payment_types'));
    }
}
