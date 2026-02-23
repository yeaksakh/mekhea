<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\ExpenseCategory;
use App\Transaction;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Modules\Connector\Transformers\ExpenseResource;
use App\Utils\BusinessUtil;

/**
 * @group Expense management
 * @authenticated
 *
 * APIs for managing expenses
 */
class RequestExpenseController extends ApiController
{
    protected $transactionUtil;

    protected $businessUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil,
    )
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * List expenses
     *
     * @queryParam location_id id of the location Example: 1
     * @queryParam payment_status payment status Example: paid
     * @queryParam start_date format:Y-m-d Example: 2018-06-25
     * @queryParam end_date format:Y-m-d Example: 2018-06-25
     * @queryParam expense_for id of the user for which expense is created
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:15
     *
     * @response {
        "data": [
            {
                "id": 59,
                "business_id": 1,
                "location_id": 1,
                "payment_status": "due",
                "ref_no": "EP2020/0001",
                "transaction_date": "2020-07-03 12:58:00",
                "total_before_tax": "50.0000",
                "tax_id": null,
                "tax_amount": "0.0000",
                "final_total": "50.0000",
                "expense_category_id": null,
                "document": null,
                "created_by": 9,
                "is_recurring": 0,
                "recur_interval": null,
                "recur_interval_type": null,
                "recur_repetitions": null,
                "recur_stopped_on": null,
                "recur_parent_id": null,
                "created_at": "2020-07-03 12:58:23",
                "updated_at": "2020-07-03 12:58:24",
                "transaction_for": {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/expense?page=1",
            "last": null,
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/expense",
            "per_page": 10,
            "to": 1
        }
    }
     */
    public function index()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $filters = request()->only(['ref_no', 'location_id', 'payment_status', 'start_date', 'end_date', 'expense_for', 'per_page']);

        $query = Transaction::from('transactions as t')
            ->where('t.business_id', $business_id)
            ->where('t.request_expense', 0)
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->leftJoin('expense_categories as ec', 'ec.id', '=', 't.expense_category_id')
            ->leftJoin('expense_categories as sc', 'sc.id', '=', 't.expense_sub_category_id')
            ->leftJoin('tax_rates as tr', 'tr.id', '=', 't.tax_id')
            ->leftJoin('users as u', 'u.id', '=', 't.created_by')
            ->leftJoin('users as expense_for', 'expense_for.id', '=', 't.expense_for')
            ->leftJoin('contacts as c', 'c.id', '=', 't.contact_id')
            ->select(
                't.*',
                'bl.name as location_name',
                'ec.name as expense_category_name',
                'sc.name as expense_sub_category_name',
                'tr.name as tax_rate_name',
                'c.name as contact_name',
                'c.mobile as contact_mobile',
                \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                \DB::raw("CONCAT(expense_for.first_name, ' ', expense_for.last_name) as expense_for_name")
            )
            ->where('t.type', 'expense')
            ->with(['transaction_for'])
            ->orderByDesc('t.transaction_date');

        $permitted_locations = $user->permitted_locations($business_id);
        if ($permitted_locations !== 'all') {
            $query->whereIn('t.location_id', $permitted_locations);
        }

        if (!empty($filters['location_id'])) {
            $query->where('t.location_id', $filters['location_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('t.payment_status', $filters['payment_status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('t.transaction_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('t.transaction_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['expense_for'])) {
            $query->where('t.expense_for', $filters['expense_for']);
        }

        if (!empty($filters['ref_no'])) {
            $query->where('t.ref_no', 'like', '%' . $filters['ref_no'] . '%');
        }

        $perPage = isset($filters['per_page']) ? (int)$filters['per_page'] : $this->perPage;

        if ($perPage === -1) {
            $expenses = $query->get();
        } else {
            $expenses = $query->paginate($perPage);
            $expenses->appends(request()->query());
        }

        return ExpenseResource::collection($expenses);
    }

    public function editActionStatus($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $is_admin = $this->businessUtil->is_admin($user, $business_id);

        // Check if the user has the necessary permissions
        if (!$is_admin && !auth()->user()->hasPermissionTo('audit_status', 'web')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch the transaction
        $transaction = Transaction::where('business_id', $business_id)
        ->where('type', 'expense')
        ->findOrFail($id);

        // Fetch all audit records associated with this transaction, including user details
        $audit = \App\Audit::with('user')  // Assuming there's a 'user' relationship defined in the Audit model
                    ->where('business_id', $business_id)
                    ->where('transaction_id', $id)  // Make sure to match 'transaction_id', not 'id'
                    ->get();

        // Return the transaction and audit data as a JSON response
        return response()->json([
            'transaction' => $transaction,
            'audit' => $audit
        ]);
    }

    /**
     * Get the specified expense / expense refund
     *
     * @urlParam expense required comma separated ids of the expenses Example: 59
     *
     * @response {
        "data": [
            {
                "id": 59,
                "business_id": 1,
                "location_id": 1,
                "payment_status": "due",
                "ref_no": "EP2020/0001",
                "transaction_date": "2020-07-03 12:58:00",
                "total_before_tax": "50.0000",
                "tax_id": null,
                "tax_amount": "0.0000",
                "final_total": "50.0000",
                "expense_category_id": null,
                "document": null,
                "created_by": 9,
                "is_recurring": 0,
                "recur_interval": null,
                "recur_interval_type": null,
                "recur_repetitions": null,
                "recur_stopped_on": null,
                "recur_parent_id": null,
                "created_at": "2020-07-03 12:58:23",
                "updated_at": "2020-07-03 12:58:24",
                "transaction_for": {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            }
        ]
    }
     */
    public function show($expense_ids)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $expense_ids = explode(',', $expense_ids);
        $expenses = Transaction::from('transactions as t')
            ->where('t.business_id', $business_id)
            ->where('t.request_expense', 0)
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->leftJoin('expense_categories as ec', 'ec.id', '=', 't.expense_category_id')
            ->leftJoin('expense_categories as sc', 'sc.id', '=', 't.expense_sub_category_id')
            ->leftJoin('tax_rates as tr', 'tr.id', '=', 't.tax_id')
            ->leftJoin('users as u', 'u.id', '=', 't.created_by') // Assuming users table with first_name and last_name
            ->leftJoin('users as expense_for', 'expense_for.id', '=', 't.expense_for')
            ->leftJoin('contacts as c', 'c.id', '=', 't.contact_id')
            ->select(
                't.*',
                'bl.name as location_name',
                'ec.name as expense_category_name',
                'sc.name as expense_sub_category_name',
                'tr.name as tax_rate_name',
                'c.name as contact_name',
                'c.mobile as contact_mobile',
                \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                \DB::raw("CONCAT(expense_for.first_name, ' ', expense_for.last_name) as expense_for_name")
            )
            ->whereIn('t.type', ['expense', 'expense_refund'])
            ->whereIn('t.id', $expense_ids)
            ->with(['transaction_for'])
            ->get();

        $permitted_locations = $user->permitted_locations($business_id);
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        return ExpenseResource::collection($expenses);
    }

    /**
     * Create expense / expense refund
     *
     * @bodyParam location_id int required id of the business location Example: 1
     * @bodyParam final_total float required Expense amount
     * @bodyParam transaction_date string transaction date format:Y-m-d H:i:s, Example: 2020-5-7 15:20:22
     * @bodyParam tax_rate_id int id of the tax rate applicable to the expense
     * @bodyParam expense_for int id of the user for which expense is created
     * @bodyParam contact_id int id of the contact(customer or supplier) for which expense is created
     * @bodyParam expense_category_id int id of the expense category
     * @bodyParam expense_sub_category_id int id of the expense sub-category
     * @bodyParam additional_notes string
     * @bodyParam is_refund int whether expense refund (0, 1) Example: 0
     * @bodyParam is_recurring int whether expense is recurring (0, 1) Example: 0
     * @bodyParam recur_interval int value of the interval expense will be regenerated
     * @bodyParam recur_interval_type string type of the recur interval ('days', 'months', 'years') Example: months
     * @bodyParam subscription_repeat_on int day of the month on which expense will be generated if recur interval type is months (1-30) Example: 15
     * @bodyParam subscription_no string subscription number
     * @bodyParam recur_repetitions int total number of expense to be generated
     * @bodyParam payment array payment lines for the expense
     *
     * @bodyParam payment.*.amount float amount of the payment Example: 453.1300
     * @bodyParam payment.*.method string payment methods ('cash', 'card', 'cheque', 'bank_transfer', 'other', 'custom_pay_1', 'custom_pay_2', 'custom_pay_3') Example: cash
     * @bodyParam payment.*.account_id int account id
     * @bodyParam payment.*.card_number string
     * @bodyParam payment.*.card_holder_name string
     * @bodyParam payment.*.card_transaction_number string
     * @bodyParam payment.*.card_type string
     * @bodyParam payment.*.card_month string
     * @bodyParam payment.*.card_year string
     * @bodyParam payment.*.card_security string
     * @bodyParam payment.*.transaction_no_1 string
     * @bodyParam payment.*.transaction_no_2 string
     * @bodyParam payment.*.transaction_no_3 string
     * @bodyParam payment.*.note string payment note
     * @bodyParam payment.*.cheque_number string
     *
     * @response {
        "data": {
            "id": 75,
            "business_id": 1,
            "location_id": "1",
            "payment_status": "due",
            "ref_no": "EP2020/0013",
            "transaction_date": "2020-07-06T05:31:29.480975Z",
            "total_before_tax": "43",
            "tax_id": null,
            "tax_amount": 0,
            "final_total": "43",
            "expense_category_id": null,
            "document": null,
            "created_by": 1,
            "is_recurring": 0,
            "recur_interval": null,
            "recur_interval_type": null,
            "recur_repetitions": null,
            "recur_stopped_on": null,
            "recur_parent_id": null,
            "created_at": "2020-07-06 11:01:29",
            "updated_at": "2020-07-06 11:01:29",
            "expense_for": []
        }
    }
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;

            DB::beginTransaction();

            $expense = $this->transactionUtil->createreqExpense($request, $business_id, $user->id);

            DB::commit();

            return new ExpenseResource($expense);
        } catch (ModelNotFoundException $e) {
            

            return $this->modelNotFoundExceptionResult($e);
        } catch (\Exception $e) {
            

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return $this->otherExceptions($e);
        }
    }

    /**
     * Update expense / expense refund
     *
     * @bodyParam final_total float Expense amount
     * @bodyParam transaction_date string transaction date format:Y-m-d H:i:s, Example: 2020-5-7 15:20:22
     * @bodyParam tax_rate_id int id of the tax rate applicable to the expense
     * @bodyParam expense_for int id of the user for which expense is created
     * @bodyParam contact_id int id of the contact(customer or supplier) for which expense is created
     * @bodyParam expense_category_id int id of the expense category
     * @bodyParam expense_sub_category_id int id of the expense sub-category
     * @bodyParam additional_notes string
     * @bodyParam is_recurring int whether expense is recurring (0, 1) Example: 0
     * @bodyParam recur_interval int value of the interval expense will be regenerated
     * @bodyParam recur_interval_type string type of the recur interval ('days', 'months', 'years') Example: months
     * @bodyParam subscription_repeat_on int day of the month on which expense will be generated if recur interval type is months (1-30) Example: 15
     * @bodyParam subscription_no string subscription number
     * @bodyParam recur_repetitions int total number of expense to be generated
     * @bodyParam payment array payment lines for the expense
     *
     *
     * @response {
        "data": {
            "id": 75,
            "business_id": 1,
            "location_id": "1",
            "payment_status": "due",
            "ref_no": "EP2020/0013",
            "transaction_date": "2020-07-06T05:31:29.480975Z",
            "total_before_tax": "43",
            "tax_id": null,
            "tax_amount": 0,
            "final_total": "43",
            "expense_category_id": null,
            "document": null,
            "created_by": 1,
            "is_recurring": 0,
            "recur_interval": null,
            "recur_interval_type": null,
            "recur_repetitions": null,
            "recur_stopped_on": null,
            "recur_parent_id": null,
            "created_at": "2020-07-06 11:01:29",
            "updated_at": "2020-07-06 11:01:29",
            "expense_for": []
        }
    }
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;

            DB::beginTransaction();

            $expense = $this->transactionUtil->updateExpense($request, $id, $business_id, false);

            DB::commit();

            return new ExpenseResource($expense);
        } catch (ModelNotFoundException $e) {
            

            return $this->modelNotFoundExceptionResult($e);
        } catch (\Exception $e) {
            

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return $this->otherExceptions($e);
        }
    }
}
