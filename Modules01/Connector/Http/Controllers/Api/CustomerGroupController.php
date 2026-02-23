<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Utils\Util;
use App\CustomerGroup;
use App\SellingPriceGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends ApiController
{
    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    public function index(Request $request)
    {
        $business_id = request()->user()->business_id;

        // Fetch combined data from customer_groups and selling_price_groups
        $customer_group = CustomerGroup::where('customer_groups.business_id', $business_id)
            ->leftJoin('selling_price_groups as spg', 'spg.id', '=', 'customer_groups.selling_price_group_id')
            ->select([
                'customer_groups.id',
                'customer_groups.name as customer_group_name',
                'customer_groups.amount',
                'customer_groups.price_calculation_type',
                'customer_groups.selling_price_group_id',
                'spg.id as selling_price_group_id',
                'spg.name as selling_price_group_name',
                'spg.description',
                'spg.business_id as selling_price_group_business_id',
                'spg.is_active'
            ]);

        if (!empty($request->input('customer_group_name'))) {
            $customer_group->where('customer_groups.name', 'like', "%{$request->input('customer_group_name')}%");
        }

        $per_page = $request->input('per_page', $this->perPage);

        if ($per_page === -1) {
            $customer_group = $customer_group->get();
        } else {
            $customer_group = $customer_group->paginate($per_page);
            $customer_group->appends($request->query());
        }

        $specific_selling_price_groups = SellingPriceGroup::where('business_id', $business_id)->get();

        // Format pagination meta and links as per your preferred structure
        $pagination = [
            'data' => $customer_group->items(),
            'links' => [
                'first' => $customer_group->url(1),
                'last' => $customer_group->url($customer_group->lastPage()),
                'prev' => $customer_group->previousPageUrl(),
                'next' => $customer_group->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $customer_group->currentPage(),
                'from' => $customer_group->firstItem(),
                'last_page' => $customer_group->lastPage(),
                'links' => $customer_group->linkCollection(), // if you want detailed links
                'path' => $customer_group->path(),
                'per_page' => $customer_group->perPage(),
                'to' => $customer_group->lastItem(),
                'total' => $customer_group->total(),
            ]
        ];

        // Return both sets of data in the response
        return response()->json([
            'customer_groups' => $pagination,
            'specific_selling_price_groups' => $specific_selling_price_groups
        ], 200);
    }


    public function store(Request $request)
    {
        try {
            $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id']);
            $input['business_id'] = $request->user()->business_id;
            $input['created_by'] = $request->user()->id;
            $input['amount'] = !empty($input['amount']) ? $this->num_uf($input['amount']) : 0;

            $customer_group = CustomerGroup::create($input);
            return response()->json(['success' => true, 'data' => $customer_group, 'msg' => __('lang_v1.success')], 201);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id']);
            $business_id = $request->user()->business_id;

            $input['amount'] = !empty($input['amount']) ? $this->num_uf($input['amount']) : 0;

            $customer_group = CustomerGroup::where('business_id', $business_id)->findOrFail($id);
            $customer_group->update($input);

            return response()->json(['success' => true, 'msg' => __('lang_v1.success')], 200);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }
    public function num_uf($input_number, $currency_details = null)
    {
        $thousand_separator = '';
        $decimal_separator = '';

        // Check if currency details are provided, if not use defaults
        if (!empty($currency_details)) {
            $thousand_separator = $currency_details['thousand_separator'] ?? '';
            $decimal_separator = $currency_details['decimal_separator'] ?? '';
        } else {
            // Use default separators if none provided
            $thousand_separator = ',';
            $decimal_separator = '.';
        }

        // Remove thousand separators from the input number
        $num = str_replace($thousand_separator, '', $input_number);

        // Replace the decimal separator with a period
        $num = str_replace($decimal_separator, '.', $num);

        // Convert to a float value and return
        return (float) $num;
    }

    public function destroy($id)
    {
        try {
            $business_id = request()->user()->business_id;

            $cg = CustomerGroup::where('business_id', $business_id)->findOrFail($id);
            $cg->delete();

            return response()->json(['success' => true, 'msg' => __('lang_v1.success')], 200);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }
}
