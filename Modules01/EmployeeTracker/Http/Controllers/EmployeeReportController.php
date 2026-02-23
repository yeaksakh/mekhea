<?php

namespace Modules\EmployeeTracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Category;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\Crm\Utils\CrmUtil;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\EmployeeTracker\Entities\EmployeeTracker;
use Modules\EmployeeTracker\Entities\EmployeeTrackerActivity;
use Modules\EmployeeTracker\Entities\EmployeeTrackerCategory;
use App\Contact;
use App\Product;
use App\BusinessLocation;

class EmployeeReportController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }



    /**
     * Builds the employee work tracking query for specified departments
     */
    private function buildEmployeeWorkQuery(
        $business_id,
        $start_date,
        $end_date,
        $user_id = null,
        $department_id = null,
        $department_names = [],
        $departmentCategoryTypes = ['hrm_department']
    ) {
        $query = User::where('users.business_id', $business_id)
            ->leftJoin('categories as dept', function ($join) use ($department_names, $departmentCategoryTypes) {
                // Support both Essentials and Socialconnects module columns on users table
                // Group the OR join conditions to ensure subsequent filters apply to both
                $join->on(function ($on) {
                    $on->on('users.essentials_department_id', '=', 'dept.id');
                })
                    ->whereIn('dept.category_type', $departmentCategoryTypes);
                if (!empty($department_names)) {
                    $join->whereIn('dept.name', $department_names);
                }
            })
            ->leftJoin('transactions', function ($join) use ($start_date, $end_date) {
                $join->on('users.id', '=', 'transactions.created_by')
                    ->whereDate('transactions.updated_at', '>=', $start_date)
                    ->whereDate('transactions.updated_at', '<=', $end_date)
                    ->whereIn('transactions.type', ['expense', 'purchase', 'sell'])
                    ->whereNull('transactions.deleted_at');
            })
            ->where('users.status', 'active')
            ->select([
                'users.id as user_id',
                'users.username',
                'users.first_name',
                'users.surname',
                'users.last_name',
                'dept.name as department',
                DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')), ' '), users.username) AS employee_name"),
                DB::raw("COUNT(DISTINCT transactions.id) as total_transactions"),
                DB::raw("COUNT(DISTINCT CASE WHEN transactions.type = 'expense' THEN transactions.id END) as expense_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN transactions.type = 'purchase' THEN transactions.id END) as purchase_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN transactions.type = 'sell' THEN transactions.id END) as sell_count"),
                DB::raw("CASE 
                    WHEN COUNT(DISTINCT transactions.id) > 0 THEN 'Working' 
                    ELSE 'Not Working' 
                END as work_status")
            ])
            ->groupBy([
                'users.id',
                'users.username',
                'users.first_name',
                'users.surname',
                'users.last_name',
                'dept.name'
            ]);

        if (!empty($user_id)) {
            $query->where('users.id', '=', $user_id);
        }

        if (!empty($department_id)) {
            $query->where('dept.id', '=', $department_id);
        }

        // Log the raw query for debugging
        \Log::info('Raw Query: ' . $query->toSql());
        \Log::info('Query Bindings: ' . json_encode($query->getBindings()));

        return $query;
    }


    private function buildEmployeeWorkQuery1(
        $business_id,
        $start_date,
        $end_date,
        $user_id = null,
        $department_id = null,
        $department_names = [],
        $departmentCategoryTypes = ['hrm_department']
    ) {
        // Base query for active users in the business
        $query = User::where('users.business_id', $business_id)
            ->where('users.status', 'active');

        // Join with departments
        $query->leftJoin('categories as dept', function ($join) use ($department_names, $departmentCategoryTypes) {
            $join->on(function ($on) {
                $on->on('users.essentials_department_id', '=', 'dept.id');
            })
                ->whereIn('dept.category_type', $departmentCategoryTypes);

            if (!empty($department_names)) {
                $join->whereIn('dept.name', $department_names);
            }
        });

        // Count expense and purchase transactions CREATED/UPDATED by user
        $query->leftJoin('transactions as expense_purchase_transactions', function ($join) use ($start_date, $end_date) {
            $join->on('users.id', '=', 'expense_purchase_transactions.created_by')
                ->whereDate('expense_purchase_transactions.updated_at', '>=', $start_date)
                ->whereDate('expense_purchase_transactions.updated_at', '<=', $end_date)
                ->whereIn('expense_purchase_transactions.type', ['expense', 'purchase'])
                ->whereNull('expense_purchase_transactions.deleted_at');
        });

        $query->select([
            'users.id as user_id',
            'users.username',
            'users.first_name',
            'users.surname',
            'users.last_name',
            'dept.name as department',
            DB::raw("COALESCE(
            NULLIF(TRIM(CONCAT(
                COALESCE(users.surname, ''), ' ', 
                COALESCE(users.first_name, ''), ' ', 
                COALESCE(users.last_name, '')
            )), ''), 
            users.username
        ) AS employee_name"),

            // Work activity counts
            DB::raw("COUNT(DISTINCT expense_purchase_transactions.id) as expense_purchase_count"),
            DB::raw("COUNT(DISTINCT CASE WHEN expense_purchase_transactions.type = 'expense' THEN expense_purchase_transactions.id END) as expense_count"),
            DB::raw("COUNT(DISTINCT CASE WHEN expense_purchase_transactions.type = 'purchase' THEN expense_purchase_transactions.id END) as purchase_count"),

            // Sales count logic for user 37
            DB::raw("CASE 
            WHEN users.id = 37 THEN (
                SELECT COUNT(DISTINCT invoice_no) 
                FROM transactions 
                WHERE type = 'sell' 
                AND DATE(updated_at) >= '$start_date' 
                AND DATE(updated_at) <= '$end_date' 
                AND audit_status != 'pending' 
                AND deleted_at IS NULL
            )
            ELSE 0 
        END as sell_audit_count"),

            // Total work activities
            DB::raw("(COUNT(DISTINCT expense_purchase_transactions.id) + 
            (CASE 
                WHEN users.id = 37 THEN (
                    SELECT COUNT(DISTINCT invoice_no) 
                    FROM transactions 
                    WHERE type = 'sell' 
                    AND DATE(updated_at) >= '$start_date' 
                    AND DATE(updated_at) <= '$end_date' 
                    AND audit_status != 'pending' 
                    AND deleted_at IS NULL
                )
                ELSE 0 
            END)
        ) as total_transactions"),

            // Work status determination
            DB::raw("CASE 
            WHEN (COUNT(DISTINCT expense_purchase_transactions.id) + 
                (CASE 
                    WHEN users.id = 37 THEN (
                        SELECT COUNT(DISTINCT invoice_no) 
                        FROM transactions 
                        WHERE type = 'sell' 
                        AND DATE(updated_at) >= '$start_date' 
                        AND DATE(updated_at) <= '$end_date' 
                        AND audit_status != 'pending' 
                        AND deleted_at IS NULL
                    )
                    ELSE 0 
                END)
            ) > 0 
            THEN 'Working' 
            ELSE 'Not Working' 
        END as work_status")
        ])
            ->groupBy([
                'users.id',
                'users.username',
                'users.first_name',
                'users.surname',
                'users.last_name',
                'dept.name'
            ]);

        // Apply filters
        if (!empty($user_id)) {
            $query->where('users.id', $user_id);
        }

        if (!empty($department_id)) {
            $query->where('dept.id', $department_id);
        }

        return $query;
    }


    public function getEmployeeWorkTrackingReport(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $start_date = $request->get('start_date') ?? now()->subDays(30)->format('Y-m-d');
        $end_date = $request->get('end_date') ?? now()->format('Y-m-d');

        try {
            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');
        } catch (\Exception $e) {
            $start_date = now()->subDays(30)->format('Y-m-d');
            $end_date = now()->format('Y-m-d');
        }

        // Determine Sales department ids dynamically (supports different naming variants)
        $sales_department_ids = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->where(function ($q) {
                $q->where('name',  ['ផ្នែកបច្ចេកវិទ្យា', 'ផ្នែកធនធានមនុស្ស']);
            })
            ->pluck('id');

        // dd($sales_department_ids);

        // Build base query without name filter and restrict to sales department ids
        $query = $this->buildEmployeeWorkQuery(
            $business_id,
            $start_date,
            $end_date,
            null,
            null,
            []
        );

        if ($sales_department_ids->isNotEmpty()) {
            $query->whereIn('dept.id', $sales_department_ids);
        } else {
            // Fallback: ensure no results if no sales department configured
            $query->whereRaw('1=0');
        }

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('users.username', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.surname', 'like', '%' . $searchValue . '%')
                    ->orWhere('dept.name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($request->has('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = [
                0 => 'users.id',
                1 => 'dept.name',
                2 => 'employee_name',
                3 => 'total_transactions',
                4 => 'work_status',
            ];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
            }
        } else {
            $query->orderBy('dept.name', 'asc')->orderBy('employee_name', 'asc');
        }

        $employees = $query->get();

        // Fallback: If no employees matched by department assignment,
        // infer Franchise activity by product category on sell lines.
        if ($employees->isEmpty()) {
            $franchiseCategoryNames = ['ហ្វ្រែនឆាយ', 'Franchise'];

            $sellAgg = DB::table('transactions as t')
                ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 't.id')
                ->join('products as p', 'p.id', '=', 'tsl.product_id')
                ->leftJoin('categories as c', function ($join) {
                    $join->on('c.id', '=', 'p.category_id')
                        ->orOn('c.id', '=', 'p.sub_category_id');
                })
                ->join('users', 'users.id', '=', 't.created_by')
                ->where('t.business_id', $business_id)
                ->whereNull('t.deleted_at')
                ->where('t.type', 'sell')
                ->whereDate('t.updated_at', '>=', $start_date)
                ->whereDate('t.updated_at', '<=', $end_date)
                ->whereIn('c.name', $franchiseCategoryNames)
                ->select([
                    'users.id as user_id',
                    DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')), ' '), users.username) AS employee_name"),
                    DB::raw('COUNT(DISTINCT t.id) as sell_count'),
                ])
                ->groupBy('users.id', 'users.username', 'users.first_name', 'users.surname', 'users.last_name')
                ->get();

            // Map to the same shape as $employees
            $employees = $sellAgg->map(function ($row) {
                return (object) [
                    'user_id' => $row->user_id,
                    'department' => 'ហ្វ្រែនឆាយ',
                    'employee_name' => $row->employee_name,
                    'total_transactions' => (int) $row->sell_count,
                    'expense_count' => 0,
                    'purchase_count' => 0,
                    'sell_count' => (int) $row->sell_count,
                    'work_status' => $row->sell_count > 0 ? 'Working' : 'Not Working',
                ];
            });
        }

        // Log employees for debugging
        $employeeIds = $employees->pluck('user_id')->all();
        $lastActivities = $this->getEmployeesLastActivities($employeeIds, $start_date, $end_date);

        $formatted_data = [];
        foreach ($employees as $employee) {
            $formatted_data[] = [
                'id' => $employee->user_id,
                'department' => $employee->department ?? 'N/A',
                'name' => $employee->employee_name,
                'total_transactions' => (int) $employee->total_transactions,
                'expense_count' => (int) ($employee->expense_count ?? 0),
                'purchase_count' => (int) ($employee->purchase_count ?? 0),
                'sell_count' => (int) ($employee->sell_count ?? 0),
                'work_status' => $employee->work_status,
                'last_activity' => $lastActivities[$employee->user_id] ?? 'No activity'
            ];
        }

        // Log formatted data for debugging

        if ($request->ajax()) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => count($formatted_data),
                'recordsFiltered' => count($formatted_data),
                'data' => $formatted_data,
            ]);
        }

        return view('employeetracker::Report.employee_work_tracking_sale', compact(
            'start_date',
            'end_date'
        ));
    }

    /**
     * Get employee work tracking report for Sales department
     */
    public function getSaleEmployeeWorkTrackingReport(Request $request)
    {
        return $this->getSalesEmployeeWorkTrackingReport($request);
    }

    /**
     * Get employee work tracking report for Sales department
     */
    public function getSalesEmployeeWorkTrackingReport(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $start_date = $request->get('start_date') ?? now()->subDays(30)->format('Y-m-d');
        $end_date = $request->get('end_date') ?? now()->format('Y-m-d');

        try {
            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');
        } catch (\Exception $e) {
            $start_date = now()->subDays(30)->format('Y-m-d');
            $end_date = now()->format('Y-m-d');
        }

        // Determine Sales department ids dynamically (supports different naming variants)
        $sales_department_ids = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->where(function ($q) {
                $q->where('name', 'ផ្នែកលក់')
                    ->orWhere('name', 'like', '%លក់%')
                    ->orWhere('name', 'like', '%Sales%');
            })
            ->pluck('id');

        // Build base query without name filter and restrict to sales department ids
        $query = $this->buildEmployeeWorkQuery(
            $business_id,
            $start_date,
            $end_date,
            null,
            null,
            []
        );

        if ($sales_department_ids->isNotEmpty()) {
            $query->whereIn('dept.id', $sales_department_ids);
        } else {
            // Fallback: ensure no results if no sales department configured
            $query->whereRaw('1=0');
        }

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('users.username', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.surname', 'like', '%' . $searchValue . '%')
                    ->orWhere('dept.name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($request->has('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = [
                0 => 'users.id',
                1 => 'dept.name',
                2 => 'employee_name',
                3 => 'total_transactions',
                4 => 'work_status',
            ];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
            }
        } else {
            $query->orderBy('dept.name', 'asc')->orderBy('employee_name', 'asc');
        }

        $employees = $query->get();

        // Efficiently fetch last activities for all employees
        $employeeIds = $employees->pluck('user_id')->all();
        $lastActivities = $this->getEmployeesLastActivities($employeeIds, $start_date, $end_date);

        $formatted_data = [];
        foreach ($employees as $employee) {
            $formatted_data[] = [
                'id' => $employee->user_id,
                'department' => $employee->department ?? 'N/A',
                'name' => $employee->employee_name,
                'total_transactions' => (int) $employee->total_transactions,
                'expense_count' => (int) $employee->expense_count,
                'purchase_count' => (int) $employee->purchase_count,
                'sell_count' => (int) $employee->sell_count,
                'work_status' => $employee->work_status,
                'last_activity' => $lastActivities[$employee->user_id] ?? 'No activity'
            ];
        }

        // Log formatted data for debugging

        if ($request->ajax()) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => count($formatted_data),
                'recordsFiltered' => count($formatted_data),
                'data' => $formatted_data,
            ]);
        }

        return view('employeetracker::Report.employee_work_tracking_sale', compact(
            'start_date',
            'end_date'
        ));
    }

    /**
     * Get employee work tracking report for Tech and HR departments
     */


    public function getTechHrEmployeeWorkTrackingReport(Request $request)
    {
        $business_id = session()->get('user.business_id');

        // Date handling with validation
        $start_date = $request->get('start_date') ?? now()->subDays(30)->format('Y-m-d');
        $end_date = $request->get('end_date') ?? now()->format('Y-m-d');

        try {
            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning('Invalid date format provided: ' . $e->getMessage());
            $start_date = now()->subDays(30)->format('Y-m-d');
            $end_date = now()->format('Y-m-d');
        }

        // Target departments (accounting and HR)
        $targetDepartments = ['គណនេយ្យ', 'ធនធានមនុស្ស'];

        // Build the main query
        $query = $this->buildEmployeeWorkQuery1(
            $business_id,
            $start_date,
            $end_date,
            null, // user_id
            null, // department_id
            $targetDepartments, // department names
            ['hrm_department'],
            true
        );

        // Ensure only users from target departments are included
        $query->whereNotNull('dept.id');

        // Search functionality
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('users.username', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.surname', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.last_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('dept.name', 'like', '%' . $searchValue . '%');
            });
        }

        // Sorting
        if ($request->has('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir', 'asc');

            $columns = [
                0 => 'users.id',
                1 => 'dept.name',
                2 => 'employee_name',
                3 => 'total_transactions',
                4 => 'work_status',
            ];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
            }
        } else {
            // Default sorting: department first, then employee name
            $query->orderBy('dept.name', 'asc')
                ->orderBy('employee_name', 'asc');
        }

        // Execute query
        $employees = $query->get();

        // Efficiently fetch last activities
        $employeeIds = $employees->pluck('user_id')->all();
        $lastActivities = $this->getEmployeesLastActivities($employeeIds, $start_date, $end_date);

        // Format data for response
        $formatted_data = $employees->map(function ($employee) use ($lastActivities) {
            return [
                'id' => $employee->user_id,
                'department' => $employee->department ?? 'N/A',
                'name' => $employee->employee_name,
                'total_transactions' => (int) $employee->total_transactions,
                'expense_count' => (int) $employee->expense_count,
                'purchase_count' => (int) $employee->purchase_count,
                'sell_audit_count' => (int) $employee->sell_audit_count,
                'work_status' => $employee->work_status,
                'last_activity' => $lastActivities[$employee->user_id] ?? 'No activity',
                // Additional metrics for better insights
                'activity_breakdown' => [
                    'created_transactions' => (int) $employee->expense_purchase_count,
                    'audited_sales' => (int) $employee->sell_audit_count
                ]
            ];
        })->toArray();

        // AJAX response for DataTables
        if ($request->ajax()) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => count($formatted_data),
                'recordsFiltered' => count($formatted_data),
                'data' => $formatted_data,
                'summary' => [
                    'total_employees' => count($formatted_data),
                    'working_employees' => collect($formatted_data)->where('work_status', 'Working')->count(),
                    'date_range' => ['start' => $start_date, 'end' => $end_date]
                ]
            ]);
        }

        // Regular view response
        return view('employeetracker::Report.employee_work_tracking_tech_hr', compact(
            'start_date',
            'end_date',
            'formatted_data'
        ));
    }


    /**
     * Get employee work tracking report for Franchise department
     */
    /**
     * Get employee work tracking report for Franchise department
     */
    public function getFranchiseEmployeeWorkTrackingReport(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $start_date = $request->get('start_date') ?? now()->subDays(30)->format('Y-m-d');
        $end_date = $request->get('end_date') ?? now()->format('Y-m-d');

        try {
            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');
        } catch (\Exception $e) {
            $start_date = now()->subDays(30)->format('Y-m-d');
            $end_date = now()->format('Y-m-d');
        }

        \Log::info('Franchise Report Debug - Business ID: ' . $business_id);
        \Log::info('Franchise Report Debug - Date Range: ' . $start_date . ' to ' . $end_date);

        // Only fetch the specific franchise department: "ផ្នែកហ្វ្រេនឆាយ"
        $franchiseDepartment = Category::where('business_id', $business_id)
            ->where(function ($q) {
                $q->where('name', 'like', '%ហ្វ្រេនឆាយ%')
                    ->orWhere('name', 'like', '%Franchise%');
            })
            ->first();

        if (!$franchiseDepartment) {
            \Log::warning('Franchise Report - No "ផ្នែកហ្វ្រេនឆាយ" department found.');
            $employees = collect([
                (object) [
                    'user_id' => 0,
                    'department' => 'No Franchise Department',
                    'employee_name' => 'No franchise employees found',
                    'total_transactions' => 0,
                    'expense_count' => 0,
                    'purchase_count' => 0,
                    'sell_count' => 0,
                    'work_status' => 'Setup Required',
                ]
            ]);
        } else {
            // Fetch users assigned to this department
            $franchiseUserCount = User::where('business_id', $business_id)
                ->where('status', 'active')
                ->where(function ($q) use ($franchiseDepartment) {
                    $q->where('essentials_department_id', $franchiseDepartment->id);
                })
                ->count();

            \Log::info('Franchise Report Debug - Users assigned to "ផ្នែកហ្វ្រេនឆាយ": ' . $franchiseUserCount);

            $employees = collect();

            if ($franchiseUserCount > 0) {
                // Use existing buildEmployeeWorkQuery with strict filtering on this department
                $query = $this->buildEmployeeWorkQuery1(
                    $business_id,
                    $start_date,
                    $end_date,
                    null,
                    $franchiseDepartment->id, // Only this department
                    []
                )->where('dept.id', $franchiseDepartment->id); // Extra safety

                $employees = collect($query->get());
            }

            // Fallback: Product-based approach using category name
            if ($employees->isEmpty()) {
                \Log::info('Franchise Report Debug - No department users, trying product-based approach...');

                $sellAgg = DB::table('transactions as t')
                    ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 't.id')
                    ->join('products as p', 'p.id', '=', 'tsl.product_id')
                    ->leftJoin('categories as c', function ($join) {
                        $join->on('c.id', '=', 'p.category_id')
                            ->orOn('c.id', '=', 'p.sub_category_id');
                    })
                    ->join('users', 'users.id', '=', 't.created_by')
                    ->where('t.business_id', $business_id)
                    ->whereNull('t.deleted_at')
                    ->where('t.type', 'sell')
                    ->whereDate('t.updated_at', '>=', $start_date)
                    ->whereDate('t.updated_at', '<=', $end_date)
                    ->where('c.name', 'ផ្នែកហ្វ្រេនឆាយ')
                    ->select([
                        'users.id as user_id',
                        'users.username',
                        'users.first_name',
                        'users.surname',
                        'users.last_name',
                        'c.name as product_category',
                        DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')), ' '), users.username) AS employee_name"),
                        DB::raw('COUNT(DISTINCT t.id) as sell_count'),
                    ])
                    ->groupBy('users.id', 'users.username', 'users.first_name', 'users.surname', 'users.last_name', 'c.name')
                    ->get();

                \Log::info('Franchise Report Debug - Product-based sales found: ' . $sellAgg->count());

                if ($sellAgg->isNotEmpty()) {
                    $employees = $sellAgg->map(function ($row) {
                        return (object) [
                            'user_id' => $row->user_id,
                            'department' => $row->product_category . ' (Product Sales)',
                            'employee_name' => $row->employee_name,
                            'total_transactions' => (int) $row->sell_count,
                            'expense_count' => 0,
                            'purchase_count' => 0,
                            'sell_count' => (int) $row->sell_count,
                            'work_status' => $row->sell_count > 0 ? 'Working' : 'Not Working',
                        ];
                    });
                }
            }

            // If still no employees found
            if ($employees->isEmpty()) {
                $employees = collect([
                    (object) [
                        'user_id' => 0,
                        'department' => 'No Franchise Department',
                        'employee_name' => 'No franchise employees found',
                        'total_transactions' => 0,
                        'expense_count' => 0,
                        'purchase_count' => 0,
                        'sell_count' => 0,
                        'work_status' => 'Setup Required',
                    ]
                ]);
            }
        }

        // Apply search filter
        $searchValue = $request->input('search.value');
        if (!empty($searchValue) && $employees->first()->user_id !== 0) {
            $employees = $employees->filter(function ($employee) use ($searchValue) {
                return stripos($employee->employee_name, $searchValue) !== false ||
                    stripos($employee->department ?? '', $searchValue) !== false;
            });
        }

        // Apply sorting
        if ($request->has('order') && $employees->first()->user_id !== 0) {
            $orderColumn = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = [
                0 => 'user_id',
                1 => 'department',
                2 => 'employee_name',
                3 => 'total_transactions',
                4 => 'work_status',
            ];

            if (isset($columns[$orderColumn])) {
                $sortField = $columns[$orderColumn];
                $employees = $employees->sortBy($sortField, SORT_REGULAR, $orderDirection === 'desc');
            }
        } else {
            $employees = $employees->sortBy('department')->sortBy('employee_name');
        }

        // Efficiently fetch last activities
        $employeeIds = $employees->pluck('user_id')->all();
        $lastActivities = $this->getEmployeesLastActivities($employeeIds, $start_date, $end_date);

        $formatted_data = [];
        foreach ($employees as $employee) {
            $formatted_data[] = [
                'id' => $employee->user_id,
                'department' => $employee->department ?? 'No Department',
                'name' => $employee->employee_name,
                'total_transactions' => (int) ($employee->total_transactions ?? 0),
                'expense_count' => (int) ($employee->expense_count ?? 0),
                'purchase_count' => (int) ($employee->purchase_count ?? 0),
                'sell_count' => (int) ($employee->sell_count ?? 0),
                'work_status' => $employee->work_status ?? 'Not Working',
                'last_activity' => $employee->user_id > 0 ? ($lastActivities[$employee->user_id] ?? 'N/A') : 'N/A'
            ];
        }

        \Log::info('Franchise Report Debug - Final formatted data count: ' . count($formatted_data));

        if ($request->ajax()) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => count($formatted_data),
                'recordsFiltered' => count($formatted_data),
                'data' => $formatted_data,
            ]);
        }

        return view('employeetracker::Report.employee_work_tracking_franchise', compact(
            'start_date',
            'end_date'
        ));
    }

    /**
     * Get the last activity dates for multiple employees in a single query.
     */
    private function getEmployeesLastActivities(array $userIds, $start_date, $end_date)
    {
        if (empty($userIds)) {
            return [];
        }

        $lastActivities = Transaction::whereIn('created_by', $userIds)
            ->whereDate('updated_at', '>=', $start_date)
            ->whereDate('updated_at', '<=', $end_date)
            ->whereIn('type', ['expense', 'purchase', 'sell'])
            ->whereNull('deleted_at')
            ->select('created_by', DB::raw('MAX(updated_at) as last_activity_at'))
            ->groupBy('created_by')
            ->pluck('last_activity_at', 'created_by');

        return $lastActivities->map(function ($activity) {
            return Carbon::parse($activity)->format('Y-m-d H:i');
        })->all();
    }

    /**
     * Get detailed work breakdown for a specific employee
     */
    public function getEmployeeWorkDetails(Request $request, $user_id)
    {
        $business_id = session()->get('user.business_id');
        $start_date = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->get('end_date', now()->format('Y-m-d'));

        $transactions = Transaction::where('created_by', $user_id)
            ->where('business_id', $business_id)
            ->whereDate('updated_at', '>=', $start_date)
            ->whereDate('updated_at', '<=', $end_date)
            ->whereIn('type', ['expense', 'purchase', 'sell'])
            ->whereNull('transactions.deleted_at')
            ->with(['contact'])
            ->select([
                'id',
                'type',
                'status',
                'invoice_no',
                'ref_no',
                'final_total',
                'contact_id',
                'transaction_date',
                'updated_at',
                'created_at'
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }


    public function getSaleReport(Request $request)
    {
        $ajaxUrl = action([EmployeeReportController::class, 'getSaleReport']);
        return $this->getEmployeeTrackerReport($request, 16, $ajaxUrl); // Pass AJAX URL
    }

    public function getFranchiseReport(Request $request)
    {
        $ajaxUrl = action([EmployeeReportController::class, 'getFranchiseReport']);
        return $this->getEmployeeTrackerReport($request, 437, $ajaxUrl); // Replace 17 with actual franchise department ID
    }

    // Add other department methods as needed
    public function getAccountingReport(Request $request)
    {
        $ajaxUrl = action([EmployeeReportController::class, 'getAccountingReport']);
        return $this->getEmployeeTrackerReport($request, 18, $ajaxUrl); // Replace 18 with actual accounting department ID
    }

    public function getHrReport(Request $request)
    {
        $ajaxUrl = action([EmployeeReportController::class, 'getHrReport']);
        return $this->getEmployeeTrackerReport($request, 541, $ajaxUrl); // Replace 18 with actual accounting department ID
    }

    public function getEmployeeTrackerReport(Request $request, $departmentId = null, $ajaxUrl = null)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $existingDepartment = EmployeeTracker::distinct()->pluck('department');
        if ($departmentId && !$existingDepartment->contains($departmentId)) {
            // Department doesn't exist, redirect back with error
            return redirect()->back()->with('error', 'Invalid department selected.');
        }

        // Get distinct employees who have activities, along with their department info
        $query = EmployeeTrackerActivity::join('users', 'employeetracker_activities.user_id', '=', 'users.id')
            ->join('employeetracker_main', 'employeetracker_activities.form_id', '=', 'employeetracker_main.id')
            ->leftJoin('categories as departments', 'employeetracker_main.department', '=', 'departments.id')
            ->where('users.business_id', $business_id)
            ->select([
                'employeetracker_activities.user_id as id',
                'employeetracker_activities.user_id',
                'users.first_name',
                'users.last_name',
                'departments.name as department_name',
                DB::raw('COUNT(employeetracker_activities.id) as activity_count'),
                DB::raw('MAX(employeetracker_activities.created_at) as last_activity_date')
            ])
            ->groupBy('employeetracker_activities.user_id', 'users.first_name', 'users.last_name', 'departments.name', DB::raw('DATE(employeetracker_activities.created_at)'));



        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Apply date filters
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $query->whereDate('employeetracker_activities.created_at', '>=', $start)
                    ->whereDate('employeetracker_activities.created_at', '<=', $end);
            }

            // Apply department filter - either from request or parameter
            $deptFilter = $departmentId ?? request()->department_1;
            if (!empty($deptFilter)) {
                $query->where('employeetracker_main.department', $deptFilter);
            }

            // Apply employee filter
            if (!empty(request()->employee_2)) {
                $query->where('employeetracker_activities.user_id', request()->employee_2);
            }

            return DataTables::of($query)
                ->addColumn('employee', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('department', function ($row) {
                    return $row->department_name ?? '';
                })
                ->addColumn('activity_count', function ($row) {
                    return $row->activity_count;
                })
                ->addColumn('last_activity', function ($row) {
                    return $row->last_activity_date ? \Carbon\Carbon::parse($row->last_activity_date)->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('status', function ($row) {
                    if ($row->activity_count > 0) {
                        return '<span class="label bg-green">' . __('employeetracker::lang.active') . '</span>';
                    } else {
                        return '<span class="label bg-yellow">' . __('employeetracker::lang.inactive') . '</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    // Only print button for report details
                    $html = '<button type="button" class="btn btn-info btn-xs print-report-btn" data-user-id="' . $row->id . '">';
                    $html .= '<i class="fa fa-print"></i> ' . __("messages.print");
                    $html .= '</button>';
                    return $html;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        // Common data for all departments
        $users = User::forDropdown($business_id, false, true, true);
        $category = EmployeeTrackerCategory::forDropdown($business_id);
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

        // Generate report name
        $reportName = 'សកម្មភាពប្រប្រចាំថ្ងៃ'; // Default report name

        // Get the specific department name if departmentId is provided
        if ($departmentId) {
            $departmentName = Category::where('id', $departmentId)
                ->where('business_id', $business_id)
                ->where('category_type', 'hrm_department')
                ->value('name');

            if ($departmentName) {
                $reportName .= ' - ' . $departmentName;
            }
        } else {
            // If no specific department, check if there's a department filter from request
            $deptFilter = request()->department_1;
            if (!empty($deptFilter)) {
                $departmentName = Category::where('id', $deptFilter)
                    ->where('business_id', $business_id)
                    ->where('category_type', 'hrm_department')
                    ->value('name');

                if ($departmentName) {
                    $reportName .= ' - ' . $departmentName;
                }
            }
        }

        // If no AJAX URL provided, default to franchise report
        if (!$ajaxUrl) {
            $ajaxUrl = action([EmployeeReportController::class, 'getFranchiseReport']);
        }

        return view('employeetracker::Report.multiple_report')
            ->with(compact(
                'module',
                'leads',
                'users',
                'reportName',
                'customer',
                'product',
                'supplier',
                'business_locations',
                'category',
                'departments',
                'designations',
                'ajaxUrl'
            ));
    }


    // public function getAccounting(Request $request)
    // {
    //     $business_id = request()->session()->get('user.business_id');

    //     $module = ModuleCreator::where('module_name', 'employeetracker')->first();

    //     $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

    //     if ((! auth()->user()->can('module.employeetracker')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     if ($request->ajax()) {
    //         $business_id = request()->session()->get('user.business_id');

    //         // Get distinct employees who have activities, along with their department info
    //         $query = EmployeeTrackerActivity::join('users', 'employeetracker_activities.user_id', '=', 'users.id')
    //             ->join('employeetracker_main', 'employeetracker_activities.form_id', '=', 'employeetracker_main.id')
    //             ->leftJoin('categories as departments', 'employeetracker_main.department', '=', 'departments.id')
    //             ->where('users.business_id', $business_id)
    //             ->select([
    //                 'employeetracker_activities.user_id as id',
    //                 'employeetracker_activities.user_id',
    //                 'users.first_name',
    //                 'users.last_name',
    //                 'departments.name as department_name',
    //                 DB::raw('COUNT(employeetracker_activities.id) as activity_count'),
    //                 DB::raw('MAX(employeetracker_activities.created_at) as last_activity_date')
    //             ])
    //             ->groupBy('employeetracker_activities.user_id', 'users.first_name', 'users.last_name', 'departments.name');

    //         if (!empty(request()->start_date) && !empty(request()->end_date)) {
    //             $start = request()->start_date;
    //             $end = request()->end_date;
    //             $query->whereDate('employeetracker_activities.created_at', '>=', $start)
    //                 ->whereDate('employeetracker_activities.created_at', '<=', $end);
    //         }

    //         if (!empty(request()->department_1)) {
    //             $query->where('employeetracker_main.department', request()->department_1);
    //         }

    //         if (!empty(request()->employee_2)) {
    //             $query->where('employeetracker_activities.user_id', request()->employee_2);
    //         }

    //         return DataTables::of($query)
    //             ->addColumn('employee', function ($row) {
    //                 return $row->first_name . ' ' . $row->last_name;
    //             })
    //             ->addColumn('department', function ($row) {
    //                 return $row->department_name ?? '';
    //             })
    //             ->addColumn('activity_count', function ($row) {
    //                 return $row->activity_count;
    //             })
    //             ->addColumn('last_activity', function ($row) {
    //                 return $row->last_activity_date ? \Carbon\Carbon::parse($row->last_activity_date)->format('Y-m-d H:i:s') : '';
    //             })
    //             ->addColumn('status', function ($row) {
    //                 if ($row->activity_count > 0) {
    //                     return '<span class="label bg-green">' . __('employeetracker::lang.active') . '</span>';
    //                 } else {
    //                     return '<span class="label bg-yellow">' . __('employeetracker::lang.inactive') . '</span>';
    //                 }
    //             })
    //             ->addColumn('action', function ($row) {
    //                 // Only print button for report details
    //                 $html = '<button type="button" class="btn btn-info btn-xs print-report-btn" data-user-id="' . $row->id . '">';
    //                 $html .= '<i class="fa fa-print"></i> ' . __("messages.print");
    //                 $html .= '</button>';
    //                 return $html;
    //             })
    //             ->rawColumns(['action', 'status'])
    //             ->make(true);
    //     }

    //     $users = User::forDropdown($business_id, false, true, true);
    //     $category = EmployeeTrackerCategory::forDropdown($business_id);
    //     $customer = Contact::where('business_id', $business_id)
    //         ->where('type', 'customer')
    //         ->pluck('name', 'id');
    //     $supplier = Contact::where('business_id', $business_id)
    //         ->where('type', 'supplier')
    //         ->pluck('supplier_business_name', 'id');
    //     $product = Product::where('business_id', $business_id)
    //         ->pluck('name', 'id');
    //     $business_locations = BusinessLocation::forDropdown($business_id, false);
    //     $departments = Category::where('business_id', $business_id)
    //         ->where('category_type', 'hrm_department')
    //         ->pluck('name', 'id');

    //     $designations = Category::where('business_id', $business_id)
    //         ->where('category_type', 'hrm_designation')
    //         ->pluck('name', 'id');
    //     $leads = $this->crmUtil->getLeadsListQuery($business_id);

    //     return view('employeetracker::Report.sale')->with(compact('module', 'leads', 'users', 'customer', 'product', 'supplier', 'business_locations', 'category', 'departments', 'designations'));
    // }
}
