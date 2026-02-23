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

class ComponentController extends Controller
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

    protected function getBusinessInfo()
    {
        $businessId = session('user.business_id');

        if (!$businessId) {
            return [
                'name' => 'Default Business',
                'location' => 'Default Location',
                'logo_exists' => false,
                'logo_url' => '',
                'business_id' => null,
                'business' => null,
                'location_object' => null,
                'user_name' => null,
                'tax_number' => null,
                'phone_number' => null,
            ];
        }

        $business = \App\Business::with('locations')
            ->leftJoin('business_locations as bl', 'business.id', '=', 'bl.business_id')
            ->leftJoin('users as u', 'u.id', '=', 'business.owner_id')
            ->leftJoin('users as creator', 'creator.id', '=', 'business.created_by')
            ->select(
                'business.id',
                'business.name',
                \DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as owner_name"),
                'u.email as owner_email',
                'u.contact_number',
                'bl.mobile',
                'bl.alternate_number',
                'bl.city',
                'bl.state',
                'bl.country',
                'bl.landmark',
                'bl.zip_code',
                'business.is_active',
                'business.created_at',
                'business.tax_number_1',
                \DB::raw("CONCAT(COALESCE(creator.surname, ''), ' ', COALESCE(creator.first_name, ''), ' ', COALESCE(creator.last_name, '')) as biz_creator")
            )
            ->where('business.id', $businessId)
            ->groupBy('business.id')
            ->first();

        $user_name = auth()->check() ? auth()->user()->username : null;
        $tax_number = $business?->tax_number_1;

        if (!$business) {
            return [
                'name' => 'Default Business',
                'location' => 'Default Location',
                'logo_exists' => false,
                'logo_url' => '',
                'business_id' => null,
                'business' => null,
                'location_object' => null,
                'user_name' => null,
                'tax_number' => null,
                'phone_number' => null,
            ];
        }

        // Get first location by ID
        $first_location = $business->locations->sortBy('id')->first();

        // Try to get phone number in order: mobile → alternate_number → user contact
        $phone_number = null;

        if ($first_location) {
            $phone_number = $first_location->mobile ?? null;
            if (empty($phone_number)) {
                $phone_number = $first_location->alternate_number ?? null;
            }
        }

        if (empty($phone_number) && $business->contact_number) {
            $phone_number = $business->contact_number;
        }

        if (empty($phone_number) && $business->mobile) {
            $phone_number = $business->mobile;
        }

        $phone_number = $phone_number ? trim($phone_number) : null;

        // Logo handling
        $logoPath = $business->logo ? 'uploads/business_logos/' . $business->logo : null;
        $logoUrl = $logoPath && file_exists(public_path($logoPath)) ? asset($logoPath) : null;

        return [
            'name' => $business->name ?? '',
            'logo_url' => $logoUrl,
            'logo_exists' => (bool)$logoUrl,
            'location' => $first_location?->location_address
                ? str_replace('<br>', ', ', $first_location->location_address)
                : 'N/A',
            'business_id' => $businessId,
            'business' => $business,
            'location_object' => $first_location,
            'user_name' => $user_name,
            'tax_number' => $tax_number,
            'phone_number' => $phone_number,
        ];
    }

    protected function getPrintButton( )
    {
        $data = [];
        $data['businessInfo'] = $this->getBusinessInfo();

        return view('employeetracker::components.printbutton', compact(
        'data'
        ));
    }

    protected function getReportHeader( )
    {
        $data = [];
        $data['businessInfo'] = $this->getBusinessInfo();

        return view('employeetracker::components.reportheader1', compact(
        'data'
        ));
    }

    protected function getReportHeader1( )
    {
        $data = [];
        $data['businessInfo'] = $this->getBusinessInfo();

        return view('employeetracker::components.reportheader2', compact(
        'data'
        ));
    }
}
