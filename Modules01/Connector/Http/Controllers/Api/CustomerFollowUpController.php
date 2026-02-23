<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Contact;
use App\Utils\ContactUtil;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Modules\Crm\Entities\CustomerAudit;
class CustomerFollowUpController extends ApiController
{
  /**
   * Constructor
   *
   * @param  ContactUtil  $contactUtil
   * @return void
   */
  public function index(Request $request)
  {
    try {
      $business_id = Auth::user()->business_id;

      if (!$business_id) {
        return response()->json([
          'error' => 'Business ID is required.'
        ], 400);
      }

      // Fetch the users for the dropdown with the correct column name
      $users = User::where('business_id', $business_id)->get(['id', 'first_name', 'last_name']);

      // Get query parameters
      $userId = $request->query('user_id');
      $customerAudits = collect();
      $contacts = collect();
      $customerType = $request->query('customer_type'); // Get customer type if provided

      if ($userId) {
        $today = now()->format('Y-m-d');
        $customerAuditsQuery = CustomerAudit::where('user_id', $userId)
          ->whereDate('created_at', $today);

        $customerAudits = $customerAuditsQuery->get();

        if ($customerAudits->isEmpty()) {
          // Modify query to filter by customer type if provided
          $contactsQuery = Contact::where('contacts.business_id', $business_id)
            ->leftJoin('user_contact_access as uca', 'contacts.id', '=', 'uca.contact_id')
            ->where('uca.user_id', $userId)
            ->select('contacts.*');

          if ($customerType) {
            $contactsQuery->where('contacts.type', $customerType); // Update with the correct column name
          }

          $contacts = $contactsQuery->get();
        }
      }

      return response()->json([
        'users' => $users,
        'contacts' => $contacts,
        'customerAudits' => $customerAudits,
        'userId' => $userId
      ]);

    } catch (\Exception $e) {
      \Log::error($e->getMessage());

      return response()->json([
        'error' => 'An error occurred: ' . $e->getMessage()
      ], 500);
    }
  }
  // public function index(Request $request)
  // {
  //   try {
  //     $business_id = Auth::user()->business_id;

  //     if (!$business_id) {
  //       return response()->json([
  //         'error' => 'Business ID is required.'
  //       ], 400);
  //     }

  //     // Fetch the users for the dropdown with the correct column name
  //     $users = User::where('business_id', $business_id)->get(['id', 'first_name', 'last_name']);

  //     // Get query parameters
  //     $userId = $request->query('user_id');
  //     $contacts = collect();
  //     $customerType = $request->query('customer_type'); // Get customer type if provided

  //     if ($userId) {
  //       // Fetch contacts where created_by = user_id
  //       $contactsQuery = Contact::where('contacts.business_id', $business_id)
  //         ->leftJoin('user_contact_access as uca', 'contacts.id', '=', 'uca.contact_id')
  //         ->where('uca.user_id', $userId)
  //         ->where('contacts.created_by', $userId) // Filter by created_by
  //         ->select('contacts.*');

  //       if ($customerType) {
  //         $contactsQuery->where('contacts.type', $customerType); // Filter by customer_type if provided
  //       }

  //       $contacts = $contactsQuery->get();
  //     }

  //     return response()->json([
  //       'users' => $users,
  //       'contacts' => $contacts,
  //       'userId' => $userId
  //     ]);

  //   } catch (\Exception $e) {
  //     \Log::error($e->getMessage());

  //     return response()->json([
  //       'error' => 'An error occurred: ' . $e->getMessage()
  //     ], 500);
  //   }
  // }

}