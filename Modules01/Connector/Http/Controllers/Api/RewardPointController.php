<?php
namespace Modules\Connector\Http\Controllers\Api;

use App\Contact;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Modules\Connector\Http\Controllers\Api\ApiController;

class RewardPointController extends ApiController
{
    protected $moduleUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;

    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
    }

    public function index(Request $request, $contact_id)
    {
        // Fetch transaction reward points for the contact with search and pagination
        $query = \App\Transaction::where('contact_id', $contact_id)
            ->select(['transaction_date', 'invoice_no', 'rp_earned', 'rp_redeemed', 'rp_redeemed_amount']);

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                    ->orWhere('transaction_date', 'LIKE', "%{$search}%");
            });
        }

        // Add pagination
        $perPage = $request->input('per_page', 10); // Default to 10 items per page
        $transactionRewards = $query->paginate($perPage);

        // Fetch contact's reward points summary
        $contactRewards = Contact::select('total_rp', 'total_rp_used', 'total_rp_expired')
            ->where('id', $contact_id)
            ->first(); // Assuming there's only one record per contact_id

        // Return the combined data as a JSON response
        return response()->json([
            'success' => true,
            'contact_rewards' => $contactRewards,
            'transaction_rewards' => $transactionRewards,
        ]);
    }
}
