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

class PrintController extends Controller
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

    public function printEmployeeReport(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $user_id = $request->get('user_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $department_id = $request->get('department_id');
        $reportName = $request->get('report_name', 'Employee Activity Report'); // Get report name

        $employee = User::findOrFail($user_id);
        $department = !empty($department_id) ? Category::findOrFail($department_id) : null;

        $activitiesQuery = EmployeeTrackerActivity::join('employeetracker_main as form', 'employeetracker_activities.form_id', '=', 'form.id')
            ->join('employeetracker_form_fields as field', 'employeetracker_activities.field_id', '=', 'field.id')
            ->where('employeetracker_activities.user_id', $user_id)
            ->where('form.business_id', $business_id)
            ->select(
                'form.name as form_name',
                'field.field_label',
                'employeetracker_activities.value',
                'employeetracker_activities.created_at'
            );

        // Apply date filter with proper date formatting
        if ($start_date && $end_date) {
            // Convert dates to proper format if needed
            $start_date_formatted = date('Y-m-d', strtotime($start_date));
            $end_date_formatted = date('Y-m-d', strtotime($end_date));

            $activitiesQuery->whereDate('employeetracker_activities.created_at', '>=', $start_date_formatted)
                ->whereDate('employeetracker_activities.created_at', '<=', $end_date_formatted);
        }

        if ($department_id) {
            $activitiesQuery->where('form.department', $department_id);
        }

        $activities = $activitiesQuery->orderBy('form.name')->orderBy('field.field_order')->get();

        $grouped_activities = $activities->groupBy('form_name');

        $businessInfo = [
            'name' => session()->get('business.name'),
            'logo_url' => session()->get('business.logo') ? url('uploads/business_logos/' . session()->get('business.logo')) : null,
            'location' => optional(BusinessLocation::find(session()->get('user.business_location_id')))->name,
            'user_name' => session()->get('user.first_name') . ' ' . session()->get('user.last_name'),
            'phone_number' => session()->get('business.phone_number', 'Phone not available'),
        ];

        // Calculate total activities count for the filtered period
        $total_activities = $activities->count();


        return view('employeetracker::PrintReport.print_employee_report_sale', compact(
            'employee',
            'department',
            'grouped_activities',
            'start_date',
            'end_date',
            'businessInfo',
            'total_activities',
            'reportName'

        ));
    }

}


////  dialy tasks tracking