<?php

namespace Modules\Connector\Http\Controllers\Api;

use Stripe\Util\Util;
use App\Utils\ContactUtil;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Modules\Connector\Http\Controllers\Api\ApiController;

class ActivityAPIController extends ApiController
{
    protected $contactUtil;
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
    ) {
        $this->contactUtil = $contactUtil;
    }
    public function index($id)
    {
        // Get the authenticated user
        $user = Auth::user();
        $business_id = $user->business_id;

        // Get the contact information
        $contact = $this->contactUtil->getContactInfo($business_id, $id);

        // Get the activities related to the contact
        $activities = Activity::forSubject($contact)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();

        // Return the data in JSON format
        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }
}