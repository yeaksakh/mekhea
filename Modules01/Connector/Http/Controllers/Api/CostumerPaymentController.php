<?php

namespace Modules\Connector\Http\Controllers\Api;

use user;
use Exception;
use App\Business;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\TransactionPayment;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\TransactionPaymentUpdated;
use Modules\Connector\Http\Controllers\Api\ApiController;

class CostumerPaymentController extends ApiController
{

    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    protected $transactionUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,

    ) {

        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }



    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $contact_id = $request->contact_id;

            // Ensure the user is authenticated and has a business_id
            if (!$user || !$user->business_id) {
                return response()->json(['error' => 'Unauthorized or business ID not found'], 401);
            }

            $business_id = $user->business_id;

            $payments = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->leftjoin('transaction_payments as parent_payment', 'transaction_payments.parent_id', '=', 'parent_payment.id')
                ->where('transaction_payments.business_id', $business_id)
                ->whereNull('transaction_payments.parent_id')
                ->with(['child_payments', 'child_payments.transaction'])
                ->where('transaction_payments.payment_for', $contact_id)
                ->select(
                    'transaction_payments.id',
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'transaction_payments.method',
                    'transaction_payments.paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.parent_id',
                    'transaction_payments.transaction_no',
                    't.invoice_no',
                    't.ref_no',
                    't.type as transaction_type',
                    't.return_parent_id',
                    't.id as transaction_id',
                    'transaction_payments.cheque_number',
                    'transaction_payments.card_transaction_number',
                    'transaction_payments.bank_account_number',
                    'transaction_payments.id as DT_RowId',
                    'parent_payment.payment_ref_no as parent_payment_ref_no'
                )
                ->groupBy('transaction_payments.id')
                ->orderByDesc('transaction_payments.paid_on')
                ->paginate();

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            // Extract pagination data
            $paginationData = $payments->toArray();

            return response()->json([
                'data' => $paginationData['data'], // Payment data
                'meta' => [
                    'current_page' => $paginationData['current_page'],
                    'last_page' => $paginationData['last_page'],
                    'per_page' => $paginationData['per_page'],
                    'total' => $paginationData['total']
                ],
                'links' => [
                    'first' => $paginationData['first_page_url'],
                    'last' => $paginationData['last_page_url'],
                    'prev' => $paginationData['prev_page_url'],
                    'next' => $paginationData['next_page_url']
                ],
                'payment_types' => $payment_types
            ]);
        } catch (Exception $e) {
            // Log the error message
            Log::error('Error fetching contact payments: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }




    public function show($payment_id)
    {
        try {
            // Get the business ID from the authenticated user
            $business_id = auth()->user()->business_id;

            // Fetch the Business object using the business ID
            $business = Business::findOrFail($business_id);

            // Find the payment line by payment ID
            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;
            if (!empty($single_payment_line->transaction_id)) {
                // Fetch the transaction related to the payment
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                    ->with(['contact', 'location', 'transaction_for'])
                    ->first();
            } else {
                // Fetch child payment if transaction_id is not present
                $child_payment = TransactionPayment::where('business_id', $business_id)
                    ->where('parent_id', $payment_id)
                    ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                    ->first();
                $transaction = !empty($child_payment) ? $child_payment->transaction : null;
            }

            // Get payment types
            $payment_types = $this->transactionUtil->payment_types($transaction->location);

            // Prepare the data for JSON response, including business info and tax fields
            $response_data = [
                'business_name' => $business->name, // Add business name
                'single_payment_line' => $single_payment_line,
                'transaction' => $transaction,
                'payment_types' => $payment_types, // Add business ID
                'tax_number_1' => $business->tax_number_1,  // Add tax_number_1 from business table
                'tax_label_1' => $business->tax_label_1,    // Add tax_label_1 from business table
                'tax_number_2' => $business->tax_number_2,  // Add tax_number_2 from business table
                'tax_label_2' => $business->tax_label_2     // Add tax_label_2 from business table
            ];

            return response()->json($response_data);
        } catch (Exception $e) {
            // Log the error message for debugging
            Log::error('Error viewing payment: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }




    public function update(Request $request, $id)
{
    $user = Auth::user();
    $business_id = $user->business_id;

    try {
        Log::info('Update method started.');

        // Validate the request inputs
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'note' => 'nullable|string',
            'card_number' => 'nullable|string',
            'card_holder_name' => 'nullable|string',
            'card_transaction_number' => 'nullable|string',
            'card_type' => 'nullable|string',
            'card_month' => 'nullable|integer',
            'card_year' => 'nullable|integer',
            'card_security' => 'nullable|string',
            'cheque_number' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'transaction_no_1' => 'nullable|string',
            'transaction_no_2' => 'nullable|string',
            'transaction_no_3' => 'nullable|string',
            'account_id' => 'nullable|integer',
            'denominations' => 'nullable|array',
            'document' => 'nullable|file'
        ]);

        $inputs = $request->only([
            'amount',
            'method',
            'note',
            'card_number',
            'card_holder_name',
            'card_transaction_number',
            'card_type',
            'card_month',
            'card_year',
            'card_security',
            'cheque_number',
            'bank_account_number'
        ]);

        $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

        // Handling custom payment methods
        if ($inputs['method'] == 'custom_pay_1') {
            $inputs['transaction_no'] = $request->input('transaction_no_1');
        } elseif ($inputs['method'] == 'custom_pay_2') {
            $inputs['transaction_no'] = $request->input('transaction_no_2');
        } elseif ($inputs['method'] == 'custom_pay_3') {
            $inputs['transaction_no'] = $request->input('transaction_no_3');
        }

        // Assign account ID if provided
        if (!empty($request->input('account_id'))) {
            $inputs['account_id'] = $request->input('account_id');
        }

        // Find payment record
        $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

        // Update cash denominations if provided
        if (!empty($request->input('denominations'))) {
            $this->transactionUtil->updateCashDenominations($payment, $request->input('denominations'));
        }

        // Update parent payment if exists
        if (!empty($payment->parent_id)) {
            $parent_payment = TransactionPayment::find($payment->parent_id);
            $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);
            $parent_payment->save();
        }

        // Retrieve transaction related to the payment
        $transaction = Transaction::where('business_id', $business_id)
            ->find($payment->transaction_id);

        $transaction_before = $transaction->replicate();

        // Upload document if provided
        $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
        if (!empty($document_name)) {
            $inputs['document'] = $document_name;
        }

        DB::beginTransaction();

        // Update the payment with new inputs
        $payment->update($inputs);
        Log::info('Payment updated successfully.');

        // Update payment status
        $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
        $transaction->payment_status = $payment_status;

        // Log the activity for the update
        $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

        DB::commit();
        Log::info('Transaction committed successfully.');

        // Trigger event after successful update
        event(new TransactionPaymentUpdated($payment, $transaction->type));

        Log::info('Event triggered successfully.');

        // Return success response
        return response()->json([
            'success' => true,
            'msg' => __('purchase.payment_updated_success'),
            'data' => [
                'payment' => $payment,
                'inputs' => $inputs,
                'transaction' => $transaction
            ]
        ]);
    } catch (Exception $e) {
        
        Log::emergency('Error in update method: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());

        // Return failure response
        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
            'error' => $e->getMessage(),
            'data' => [
                'payment' => isset($payment) ? $payment : null,
                'inputs' => isset($inputs) ? $inputs : null,
                'transaction' => isset($transaction) ? $transaction : null
            ]
        ]);
    }
}

    // this is the function of update above that modifide from util
    public function num_uf($input_number, $currency_details = null)
    {
        // Default separators if currency details are not provided
        $default_thousand_separator = ','; // Default thousand separator
        $default_decimal_separator = '.';  // Default decimal separator

        // Use provided currency details or fallback to defaults
        $thousand_separator = $currency_details->thousand_separator ?? $default_thousand_separator;
        $decimal_separator = $currency_details->decimal_separator ?? $default_decimal_separator;

        // Replace thousand and decimal separators in the input number
        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float) $num;
    }

    // this is the function of update above that modifide from util


    public function destroy($id)
    {

        try {

            $payment = TransactionPayment::findOrFail($id);

            DB::beginTransaction();

            if (!empty($payment->transaction_id)) {
                // Delete payment linked to a transaction
                TransactionPayment::deletePayment($payment);
            } else { // Handle advance payment
                $adjusted_payments = TransactionPayment::where('parent_id', $payment->id)->get();

                $total_adjusted_amount = $adjusted_payments->sum('amount');

                // Get customer advance share from payment and deduct from advance balance
                $total_customer_advance = $payment->amount - $total_adjusted_amount;
                if ($total_customer_advance > 0) {
                    $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance, 'deduct');
                }

                // Delete all child payments
                foreach ($adjusted_payments as $adjusted_payment) {
                    // Make parent payment null as it will get deleted
                    $adjusted_payment->parent_id = null;
                    TransactionPayment::deletePayment($adjusted_payment);
                }

                // Delete the advance payment
                TransactionPayment::deletePayment($payment);
            }

            DB::commit();

            // Return success response
            return response()->json(['success' => true, 'msg' => __('purchase.payment_deleted_success')]);
        } catch (\Exception $e) {
            
            Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }
}
