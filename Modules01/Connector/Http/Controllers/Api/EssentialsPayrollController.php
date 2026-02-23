<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\BusinessLocation;
use App\Category;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Essentials\Entities\EssentialsLeave;
use Modules\Essentials\Entities\EssentialsLeaveType;
use Modules\Essentials\Notifications\NewLeaveNotification;
use Modules\Essentials\Notifications\LeaveStatusNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Modules\Essentials\Entities\EssentialsAllowanceAndDeduction;
use Modules\Essentials\Utils\EssentialsUtil;

class EssentialsPayrollController extends Controller
{
    protected $moduleUtil;

    protected $essentialsUtil;

    protected $commonUtil;

    protected $transactionUtil;

    protected $businessUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EssentialsUtil $essentialsUtil, Util $commonUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->essentialsUtil = $essentialsUtil;
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    public function getMyPayrolls(Request $request)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;

            // Check permission
            if (! $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            // Get payroll query
            $payrolls = $this->essentialsUtil->getPayrollQuery($business_id)
                ->where('transactions.expense_for', $user->id)
                ->get();

            // Format payroll data
            $formatted_payrolls = $payrolls->map(function ($row) {
                $transaction_date = \Carbon::parse($row->transaction_date);

                $base_salary = $row->essentials_duration * $row->essentials_amount_per_unit_duration;

                return [
                    'id' => $row->id,
                    'transaction_date' => $transaction_date->format('F Y'),
                    'final_total' => $row->final_total,
                    'payment_status' => $row->payment_status,
                    'basic_salary' => $base_salary,
             
                ];
            });

            // Get pay components
            $pay_components = EssentialsAllowanceAndDeduction::join(
                'essentials_user_allowance_and_deductions as EUAD',
                'essentials_allowances_and_deductions.id',
                '=',
                'EUAD.allowance_deduction_id'
            )
                ->where('essentials_allowances_and_deductions.business_id', $business_id)
                ->where('EUAD.user_id', $user->id)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'payrolls' => $formatted_payrolls,
                    'pay_components' => $pay_components
                ],
                'message' => 'Payrolls retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payrolls: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;

            // Authorization check
            if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            // Build query
            $query = Transaction::where('business_id', $business_id)
                ->with(['transaction_for', 'payment_lines']);

            if (!$user->can('essentials.view_all_payroll')) {
                $query->where('expense_for', $user->id);
            }

            $payroll = $query->findOrFail($id);

            // Process dates
            $transaction_date = \Carbon::parse($payroll->transaction_date);
            $start_of_month = \Carbon::parse($payroll->transaction_date);
            $end_of_month = $start_of_month->copy()->endOfMonth();

            // Get related data
            $department = Category::where('category_type', 'hrm_department')
                ->find($payroll->transaction_for->essentials_department_id);

            $designation = Category::where('category_type', 'hrm_designation')
                ->find($payroll->transaction_for->essentials_designation_id);

            $location = BusinessLocation::where('business_id', $business_id)
                ->find($payroll->transaction_for->location_id);

            // Decode JSON fields
            $allowances = !empty($payroll->essentials_allowances) ? json_decode($payroll->essentials_allowances, true) : [];
            $deductions = !empty($payroll->essentials_deductions) ? json_decode($payroll->essentials_deductions, true) : [];

            // Financial calculations
            $base_salary = $payroll->essentials_duration * $payroll->essentials_amount_per_unit_duration;
            $total_earnings = $base_salary + array_sum($allowances['allowance_amounts'] ?? []);
            $total_deductions = array_sum($deductions['deduction_amounts'] ?? []);
            $net_pay = $total_earnings - $total_deductions;

            $bank_details = json_decode($payroll->transaction_for->bank_details, true);

            // Calculate leaves
            $leaves = EssentialsLeave::where('business_id', $business_id)
                ->where('user_id', $payroll->transaction_for->id)
                ->whereDate('start_date', '>=', $start_of_month)
                ->whereDate('end_date', '<=', $end_of_month)
                ->get();

            $total_leaves = $leaves->sum(function ($leave) {
                return \Carbon::parse($leave->start_date)->diffInDays($leave->end_date) + 1;
            });

            // Additional calculations
            $days_in_month = $start_of_month->daysInMonth;
            $total_days_present = $this->essentialsUtil->getTotalDaysWorkedForGivenDateOfAnEmployee(
                $business_id,
                $payroll->transaction_for->id,
                $start_of_month->format('Y-m-d'),
                $end_of_month->format('Y-m-d')
            );

            $total_work_duration = $this->essentialsUtil->getTotalWorkDuration(
                'hour',
                $payroll->transaction_for->id,
                $business_id,
                $start_of_month->format('Y-m-d'),
                $end_of_month->format('Y-m-d')
            );

            // Format deductions for the response
            $formatted_deductions = array_map(
                function ($name, $amount, $type, $percent) {
                    return [
                        'name' => $name,
                        'amount' => $amount,
                        'type' => $type,
                        'percent' => ($type === 'percent' && isset($percent)) ? $percent : 0
                    ];
                },
                $deductions['deduction_names'] ?? [],
                $deductions['deduction_amounts'] ?? [],
                $deductions['deduction_types'] ?? [],
                $deductions['deduction_percents'] ?? []
            );

            // Prepare response data
            $response_data = [
                'id' => $payroll->id,
                'month' => $transaction_date->format('F'),
                'year' => $transaction_date->format('Y'),
                'transaction_date' => $transaction_date->format('Y-m-d'),
                'final_total' => $payroll->final_total,
                'final_total_in_words' => $this->commonUtil->numToIndianFormat($payroll->final_total),
                'allowances' => array_map(
                    function ($name, $amount, $type, $percent) {
                        return [
                            'name' => $name,
                            'amount' => $amount,
                            'rate' => ($type === 'percent' && isset($percent)) ? $percent . '%' : null
                        ];
                    },
                    $allowances['allowance_names'] ?? [],
                    $allowances['allowance_amounts'] ?? [],
                    $allowances['allowance_types'] ?? [],
                    $allowances['allowance_percents'] ?? []
                ),
                'deductions' => $formatted_deductions, // Use the formatted deductions
                'payment_types' => $this->moduleUtil->payment_types(),
                'bank_details' => $bank_details,
                'department' => $department ? [
                    'id' => $department->id,
                    'name' => $department->name
                ] : null,
                'basic_salary' => [
                    'amount' => $base_salary,
                    'details' => [
                        'duration' => $payroll->essentials_duration,
                        'unit' => $payroll->essentials_duration_unit,
                        'rate' => $payroll->essentials_amount_per_unit_duration
                    ]
                ],
                'total_earnings' => $total_earnings,
                'total_deductions' => $total_deductions,
                'net_pay' => $net_pay, // Add net pay to the response
                'designation' => $designation ? [
                    'id' => $designation->id,
                    'name' => $designation->name
                ] : null,
                'location' => $location ? [
                    'id' => $location->id,
                    'name' => $location->name
                ] : null,
                'leaves' => $leaves->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'days' => \Carbon::parse($leave->start_date)->diffInDays($leave->end_date) + 1
                    ];
                })->values(),
                'statistics' => [
                    'total_leaves' => $total_leaves,
                    'days_in_month' => $days_in_month,
                    'total_days_present' => $total_days_present,
                    'total_work_duration_hours' => $total_work_duration
                ],
                'employee' => $payroll->transaction_for
            ];

            return response()->json([
                'success' => true,
                'data' => $response_data,
                'message' => 'Payroll details retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payroll: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ], 500);
        }
    }
}