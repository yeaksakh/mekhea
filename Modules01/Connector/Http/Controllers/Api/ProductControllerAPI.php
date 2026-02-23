<?php

namespace Modules\Connector\Http\Controllers\Api;

use Excel;
use App\Unit;
use App\Media;
use App\Brands;
use App\Product;
use App\TaxRate;
use App\Discount;
use App\Business;
use App\Category;
use App\Warranty;
use App\Variation;
use App\BusinessLocation;
use App\ProductVariation;
use App\Utils\ModuleUtil;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\VariationTemplate;
use App\CustomerGroup;
use App\VariationGroupPrice;
use Illuminate\Http\Request;
use App\VariationValueTemplate;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Events\ProductsCreatedOrModified;
use Modules\Connector\Transformers\CommonResource;
use Modules\Connector\Transformers\ProductResource;
use Modules\Connector\Transformers\PublicProductResource;
use Modules\Connector\Transformers\VariationResource;

/**
 * @group Product management
 * @authenticated
 *
 * APIs for managing products
 */
class ProductControllerAPI extends ApiController
{


    protected $productUtil;

    protected $moduleUtil;

    private $barcode_types;


    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * List products
     *
     * @queryParam order_by Values: product_name or newest
     * @queryParam order_direction Values: asc or desc
     * @queryParam brand_id comma separated ids of one or multiple brands
     * @queryParam category_id comma separated ids of one or multiple category
     * @queryParam sub_category_id comma separated ids of one or multiple sub-category
     * @queryParam location_id Example: 1
     * @queryParam selling_price_group (1, 0)
     * @queryParam send_lot_detail Send lot details in each variation location details(1, 0)
     * @queryParam name Search term for product name
     * @queryParam sku Search term for product sku
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Men's Reverse Fleece Crew",
                "business_id": 1,
                "type": "single",
                "sub_unit_ids": null,
                "enable_stock": 1,
                "alert_quantity": "5.0000",
                "sku": "AS0001",
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "image": null,
                "woocommerce_media_id": null,
                "product_description": null,
                "created_by": 1,
                "warranty_id": null,
                "is_inactive": 0,
                "repair_model_id": null,
                "not_for_selling": 0,
                "ecom_shipping_class_id": null,
                "ecom_active_in_store": 1,
                "woocommerce_product_id": 356,
                "woocommerce_disable_sync": 0,
                "image_url": "http://local.pos.com/img/default.png",
                "product_variations": [
                    {
                        "id": 1,
                        "variation_template_id": null,
                        "name": "DUMMY",
                        "product_id": 1,
                        "is_dummy": 1,
                        "created_at": "2018-01-03 21:29:08",
                        "updated_at": "2018-01-03 21:29:08",
                        "variations": [
                            {
                                "id": 1,
                                "name": "DUMMY",
                                "product_id": 1,
                                "sub_sku": "AS0001",
                                "product_variation_id": 1,
                                "woocommerce_variation_id": null,
                                "variation_value_id": null,
                                "default_purchase_price": "130.0000",
                                "dpp_inc_tax": "143.0000",
                                "profit_percent": "0.0000",
                                "default_sell_price": "130.0000",
                                "sell_price_inc_tax": "143.0000",
                                "created_at": "2018-01-03 21:29:08",
                                "updated_at": "2020-06-09 00:23:22",
                                "deleted_at": null,
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 56,
                                        "product_id": 1,
                                        "product_variation_id": 1,
                                        "variation_id": 1,
                                        "location_id": 1,
                                        "qty_available": "20.0000",
                                        "created_at": "2020-06-08 23:46:40",
                                        "updated_at": "2020-06-08 23:46:40"
                                    }
                                ],
                                "media": [
                                    {
                                        "id": 1,
                                        "business_id": 1,
                                        "file_name": "1591686466_978227300_nn.jpeg",
                                        "description": null,
                                        "uploaded_by": 9,
                                        "model_type": "App\\Variation",
                                        "woocommerce_media_id": null,
                                        "model_id": 1,
                                        "created_at": "2020-06-09 00:07:46",
                                        "updated_at": "2020-06-09 00:07:46",
                                        "display_name": "nn.jpeg",
                                        "display_url": "http://local.pos.com/uploads/media/1591686466_978227300_nn.jpeg"
                                    }
                                ],
                                "discounts": [
                                    {
                                        "id": 2,
                                        "name": "FLAT 10%",
                                        "business_id": 1,
                                        "brand_id": null,
                                        "category_id": null,
                                        "location_id": 1,
                                        "priority": 2,
                                        "discount_type": "fixed",
                                        "discount_amount": "5.0000",
                                        "starts_at": "2021-09-01 11:45:00",
                                        "ends_at": "2021-09-30 11:45:00",
                                        "is_active": 1,
                                        "spg": null,
                                        "applicable_in_cg": 1,
                                        "created_at": "2021-09-01 11:46:00",
                                        "updated_at": "2021-09-01 12:12:55",
                                        "formated_starts_at": " 11:45",
                                        "formated_ends_at": " 11:45"
                                    }
                                ],
                                "selling_price_group": [
                                    {
                                        "id": 2,
                                        "variation_id": 1,
                                        "price_group_id": 1,
                                        "price_inc_tax": "140.0000",
                                        "created_at": "2020-06-09 00:23:31",
                                        "updated_at": "2020-06-09 00:23:31"
                                    }
                                ]
                            }
                        ]
                    }
                ],
                "brand": {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                },
                "unit": {
                    "id": 1,
                    "business_id": 1,
                    "actual_name": "Pieces",
                    "short_name": "Pc(s)",
                    "allow_decimal": 0,
                    "base_unit_id": null,
                    "base_unit_multiplier": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 15:15:20",
                    "updated_at": "2018-01-03 15:15:20"
                },
                "category": {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34"
                },
                "sub_category": {
                    "id": 5,
                    "name": "Shirts",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 1,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:08:18",
                    "updated_at": "2018-01-03 21:08:18"
                },
                "product_tax": {
                    "id": 1,
                    "business_id": 1,
                    "name": "VAT@10%",
                    "amount": 10,
                    "is_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:40:07",
                    "updated_at": "2018-01-04 02:40:07"
                },
                 "product_locations": [
                {
                    "id": 1,
                    "business_id": 1,
                    "location_id": null,
                    "name": "Awesome Shop",
                    "landmark": "Linking Street",
                    "country": "USA",
                    "state": "Arizona",
                    "city": "Phoenix",
                    "zip_code": "85001",
                    "invoice_scheme_id": 1,
                    "invoice_layout_id": 1,
                    "selling_price_group_id": null,
                    "print_receipt_on_invoice": 1,
                    "receipt_printer_type": "browser",
                    "printer_id": null,
                    "mobile": null,
                    "alternate_number": null,
                    "email": null,
                    "website": null,
                    "featured_products": [
                        "5",
                        "71"
                    ],
                    "is_active": 1,
                    "default_payment_accounts": "{\"cash\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"card\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"cheque\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"bank_transfer\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"other\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"custom_pay_1\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"custom_pay_3\":{\"is_enabled\":\"1\",\"account\":\"3\"}}",
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:20",
                    "updated_at": "2020-06-09 01:07:05",
                    "pivot": {
                        "product_id": 2,
                        "location_id": 1
                    }
                }]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/product?page=1",
            "last": "http://local.pos.com/connector/api/product?page=32",
            "prev": null,
            "next": "http://local.pos.com/connector/api/product?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/product",
            "per_page": 10,
            "to": 10
        }
    }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;


        $filters = request()->only(['brand_id', 'category_id', 'location_id', 'sub_category_id', 'per_page']);
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;


        $search = request()->only(['sku', 'name']);
        //order
        $order_by = null;
        $order_direction = null;
        $sellingPriceGroupId = request()->input('customer_group_id');

        if (!empty(request()->input('order_by'))) {
            $order_by = in_array(request()->input('order_by'), ['product_name', 'newest']) ? request()->input('order_by') : null;
            $order_direction = in_array(request()->input('order_direction'), ['asc', 'desc']) ? request()->input('order_direction') : 'asc';
        }

        $products = $this->__getProducts($business_id, $filters, $search, true, $order_by, $order_direction);

        return ProductResource::collection($products);
    }

    /**
     * Get the specified product
     *
     * @urlParam product required comma separated ids of products Example: 1
     * @queryParam selling_price_group (1, 0)
     * @queryParam send_lot_detail Send lot details in each variation location details(1, 0)
     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's Reverse Fleece Crew",
                    "business_id": 1,
                    "type": "single",
                    "sub_unit_ids": null,
                    "enable_stock": 1,
                    "alert_quantity": "5.0000",
                    "sku": "AS0001",
                    "barcode_type": "C128",
                    "expiry_period": null,
                    "expiry_period_type": null,
                    "enable_sr_no": 0,
                    "weight": null,
                    "product_custom_field1": null,
                    "product_custom_field2": null,
                    "product_custom_field3": null,
                    "product_custom_field4": null,
                    "image": null,
                    "woocommerce_media_id": null,
                    "product_description": null,
                    "created_by": 1,
                    "warranty_id": null,
                    "is_inactive": 0,
                    "repair_model_id": null,
                    "not_for_selling": 0,
                    "ecom_shipping_class_id": null,
                    "ecom_active_in_store": 1,
                    "woocommerce_product_id": 356,
                    "woocommerce_disable_sync": 0,
                    "image_url": "http://local.pos.com/img/default.png",
                    "product_variations": [
                        {
                            "id": 1,
                            "variation_template_id": null,
                            "name": "DUMMY",
                            "product_id": 1,
                            "is_dummy": 1,
                            "created_at": "2018-01-03 21:29:08",
                            "updated_at": "2018-01-03 21:29:08",
                            "variations": [
                                {
                                    "id": 1,
                                    "name": "DUMMY",
                                    "product_id": 1,
                                    "sub_sku": "AS0001",
                                    "product_variation_id": 1,
                                    "woocommerce_variation_id": null,
                                    "variation_value_id": null,
                                    "default_purchase_price": "130.0000",
                                    "dpp_inc_tax": "143.0000",
                                    "profit_percent": "0.0000",
                                    "default_sell_price": "130.0000",
                                    "sell_price_inc_tax": "143.0000",
                                    "created_at": "2018-01-03 21:29:08",
                                    "updated_at": "2020-06-09 00:23:22",
                                    "deleted_at": null,
                                    "combo_variations": null,
                                    "variation_location_details": [
                                        {
                                            "id": 56,
                                            "product_id": 1,
                                            "product_variation_id": 1,
                                            "variation_id": 1,
                                            "location_id": 1,
                                            "qty_available": "20.0000",
                                            "created_at": "2020-06-08 23:46:40",
                                            "updated_at": "2020-06-08 23:46:40"
                                        }
                                    ],
                                    "media": [
                                        {
                                            "id": 1,
                                            "business_id": 1,
                                            "file_name": "1591686466_978227300_nn.jpeg",
                                            "description": null,
                                            "uploaded_by": 9,
                                            "model_type": "App\\Variation",
                                            "woocommerce_media_id": null,
                                            "model_id": 1,
                                            "created_at": "2020-06-09 00:07:46",
                                            "updated_at": "2020-06-09 00:07:46",
                                            "display_name": "nn.jpeg",
                                            "display_url": "http://local.pos.com/uploads/media/1591686466_978227300_nn.jpeg"
                                        }
                                    ],
                                    "discounts": [
                                        {
                                            "id": 2,
                                            "name": "FLAT 10%",
                                            "business_id": 1,
                                            "brand_id": null,
                                            "category_id": null,
                                            "location_id": 1,
                                            "priority": 2,
                                            "discount_type": "fixed",
                                            "discount_amount": "5.0000",
                                            "starts_at": "2021-09-01 11:45:00",
                                            "ends_at": "2021-09-30 11:45:00",
                                            "is_active": 1,
                                            "spg": null,
                                            "applicable_in_cg": 1,
                                            "created_at": "2021-09-01 11:46:00",
                                            "updated_at": "2021-09-01 12:12:55",
                                            "formated_starts_at": " 11:45",
                                            "formated_ends_at": " 11:45"
                                        }
                                    ],
                                    "selling_price_group": [
                                        {
                                            "id": 2,
                                            "variation_id": 1,
                                            "price_group_id": 1,
                                            "price_inc_tax": "140.0000",
                                            "created_at": "2020-06-09 00:23:31",
                                            "updated_at": "2020-06-09 00:23:31"
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "brand": {
                        "id": 1,
                        "business_id": 1,
                        "name": "Levis",
                        "description": null,
                        "created_by": 1,
                        "deleted_at": null,
                        "created_at": "2018-01-03 21:19:47",
                        "updated_at": "2018-01-03 21:19:47"
                    },
                    "unit": {
                        "id": 1,
                        "business_id": 1,
                        "actual_name": "Pieces",
                        "short_name": "Pc(s)",
                        "allow_decimal": 0,
                        "base_unit_id": null,
                        "base_unit_multiplier": null,
                        "created_by": 1,
                        "deleted_at": null,
                        "created_at": "2018-01-03 15:15:20",
                        "updated_at": "2018-01-03 15:15:20"
                    },
                    "category": {
                        "id": 1,
                        "name": "Men's",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "product",
                        "description": null,
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2018-01-03 21:06:34",
                        "updated_at": "2018-01-03 21:06:34"
                    },
                    "sub_category": {
                        "id": 5,
                        "name": "Shirts",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 1,
                        "created_by": 1,
                        "category_type": "product",
                        "description": null,
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2018-01-03 21:08:18",
                        "updated_at": "2018-01-03 21:08:18"
                    },
                    "product_tax": {
                        "id": 1,
                        "business_id": 1,
                        "name": "VAT@10%",
                        "amount": 10,
                        "is_tax_group": 0,
                        "created_by": 1,
                        "woocommerce_tax_rate_id": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:40:07",
                        "updated_at": "2018-01-04 02:40:07"
                    },
                    "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": [
                            "5",
                            "71"
                        ],
                        "is_active": 1,
                        "default_payment_accounts": "{\"cash\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"card\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"cheque\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"bank_transfer\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"other\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"custom_pay_1\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"custom_pay_3\":{\"is_enabled\":\"1\",\"account\":\"3\"}}",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2020-06-09 01:07:05",
                        "pivot": {
                            "product_id": 2,
                            "location_id": 1
                        }
                    }]
                }
            ]
        }
     */
    public function show($product_ids)
    {
        $user = Auth::user();

        // if (!$user->can('api.access')) {
        //     return $this->respondUnauthorized();
        // }

        $business_id = $user->business_id;
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        $filters['product_ids'] = explode(',', $product_ids);

        $products = $this->__getProducts($business_id, $filters);

        return ProductResource::collection($products);
    }

    /**
     * Function to query product
     *
     * @return Response
     */
    private function __getProducts($business_id, $filters = [], $search = [], $pagination = false, $order_by = null, $order_direction = null)
    {
        $query = Product::where('business_id', $business_id);

        $with = [
            'product_variations.variations.variation_location_details',
            'brand',
            'unit',
            'category',
            'sub_category',
            'product_tax',
            'product_variations.variations.media',
            'product_variations.variations.group_prices',
            'product_locations'
        ];

        // [Existing filter conditions remain unchanged...]
        if (!empty($filters['category_id'])) {
            $category_ids = explode(',', $filters['category_id']);
            $query->whereIn('category_id', $category_ids);
        }

        if (!empty($filters['sub_category_id'])) {
            $sub_category_id = explode(',', $filters['sub_category_id']);
            $query->whereIn('sub_category_id', $sub_category_id);
        }

        if (!empty($filters['brand_id'])) {
            $brand_ids = explode(',', $filters['brand_id']);
            $query->whereIn('brand_id', $brand_ids);
        }

        if (!empty($filters['selling_price_group']) && $filters['selling_price_group'] == true) {
            $with[] = 'product_variations.variations.group_prices';
        }

        if (!empty($filters['location_id'])) {
            $location_id = $filters['location_id'];
            $query->whereHas('product_locations', function ($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['product_variations.variations.variation_location_details'] = function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product_locations'] = function ($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        if (!empty($filters['product_ids'])) {
            $query->whereIn('id', $filters['product_ids']);
        }

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                if (!empty($search['name'])) {
                    $query->where('products.name', 'like', '%' . $search['name'] . '%');
                }

                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('sku', 'like', '%' . $sku . '%');
                    $query->orWhereHas('variations', function ($q) use ($sku) {
                        $q->where('variations.sub_sku', 'like', '%' . $sku . '%');
                    });
                }
            });
        }

        if (!empty($order_by)) {
            if ($order_by == 'product_name') {
                $query->orderBy('products.name', $order_direction);
            }

            if ($order_by == 'newest') {
                $query->orderBy('products.id', $order_direction);
            }
        }

        $query->with($with);

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($pagination && $perPage != -1) {
            $products = $query->paginate($perPage);
            $products->appends(request()->query());
        } else {
            $products = $query->get();
        }

        $customerGroups = CustomerGroup::where('customer_groups.business_id', $business_id)
            ->leftJoin('selling_price_groups as spg', 'spg.id', '=', 'customer_groups.selling_price_group_id')
            ->select(
                'customer_groups.id as customer_group_id',
                'customer_groups.name as customer_group_name',
                'spg.id as selling_price_group_id'
            )
            ->get();

        $discounts = Discount::where('discounts.business_id', $business_id)
            ->select('id', 'spg', 'discount_amount', 'discount_type', 'starts_at', 'ends_at')
            ->get();

        $discountVariations = DB::table('discount_variations')
            ->select('discount_id', 'variation_id')
            ->get();

        $currentDate = now();

        // Transform products
        if ($pagination && $perPage != -1) {
            $products->getCollection()->transform(function ($product) use ($customerGroups, $discounts, $currentDate, $discountVariations,$filters) {
                $product->customer_groups = $customerGroups->map(function ($group) use ($product, $discounts, $currentDate, $discountVariations,$filters) {
                    $variation_prices = [];

                    // Collect prices from all variations
                    if ($product->product_variations) {
                        foreach ($product->product_variations as $productVariation) {
                            if ($productVariation->variations) {
                                foreach ($productVariation->variations as $variation) {
                                    $groupPrice = $variation->group_prices
                                        ->where('price_group_id', $group->selling_price_group_id)
                                        ->first();

                                    $variantId = (int)($variation->product_variation_id ?? $variation->id);

                                    // Use collection method to find matching discount IDs
                                    $promotionVariants = $discountVariations
                                        ->where('variation_id', $variantId)
                                        ->pluck('discount_id')
                                        ->values()
                                        ->toArray();

                                    // Map discount details to include customer group fields
                                    $discountDetails = $discounts
                                        ->whereIn('id', $promotionVariants)
                                        ->map(function ($discount) use ($group) {
                                            return [
                                                'customer_group_id' => $group->customer_group_id,
                                                'customer_group_name' => $group->customer_group_name,
                                                'selling_price_group_id' => $group->selling_price_group_id,
                                                'discount_id' => $discount->id,
                                                'promotion_amount' => $discount->discount_amount,
                                                'promotion_type' => $discount->discount_type,
                                                'promotion_start' => $discount->starts_at,
                                                'promotion_end' => $discount->ends_at,
                                            ];
                                        })->values()->toArray();

                                    // Add to variation prices
                                    $variation_prices[] = [
                                        'variant_id' => $variation->id,
                                        'amount' => $groupPrice?->price_inc_tax ?? $variation->default_sell_price,
                                        'type' => $groupPrice?->price_type ?? null,
                                        'promotions' => $discountDetails,
                                    ];
                                }
                            }
                        }
                    }

                    $discount_amount = null;
                    $discount_type = null;
                    $promotion_amount = $variation_prices[0]['promotions'] ?? [];

                    $discount_amount = !empty($variation_prices) ? $variation_prices[0]['amount'] : null;
                    $discount_type = !empty($variation_prices) ? $variation_prices[0]['type'] : null;

                    return [
                        'customer_group_id' => $group->customer_group_id,
                        'customer_group_name' => $group->customer_group_name,
                        'selling_price_group_id' => $group->selling_price_group_id,
                        'discount_amount' => $discount_amount,
                        'discount_type' => $discount_type,
                        'promotion' => $promotion_amount,
                    ];
                })->toArray();

                if ($product->product_variations) {
                    foreach ($product->product_variations as $productVariation) {
                        if ($productVariation->variations) {
                            foreach ($productVariation->variations as $variation) {
                                $variation->variant_id = $variation->id;
                                $variation->amount = $variation->default_sell_price;

                                // Convert quantity in combo_variations to string
                                if (!empty($variation->combo_variations)) {
                                    $variation_ids = array_column($variation->combo_variations, 'variation_id');
                                    $variation->combo_variations = array_map(function ($combo) {
                                        $combo['quantity'] = (string) $combo['quantity'];
                                        return $combo;
                                    }, $variation->combo_variations);
                                    $combo_stock = VariationLocationDetails::whereIn('product_variation_id', $variation_ids)
                                    ->where('location_id', $filters['location_id'])
                                    ->get();
                                    $min_qty_available = $combo_stock->min('qty_available');
                                    $variation->combo_qty = $min_qty_available ?? 0;
                                }
                            }
                        }
                    }
                }

                return $product;
            });
        } else {
            $products->each(function ($product) use ($customerGroups, $discounts, $currentDate,$filters) {
                $product->customer_groups = $customerGroups->map(function ($group) use ($product, $discounts, $currentDate,$filters) {
                    $variation_prices = [];

                    // Collect prices from all variations
                    if ($product->product_variations) {
                        foreach ($product->product_variations as $productVariation) {
                            if ($productVariation->variations) {
                                foreach ($productVariation->variations as $variation) {
                                    $groupPrice = $variation->group_prices
                                        ->where('price_group_id', $group->selling_price_group_id)
                                        ->first();
                                    $variation_prices[] = [
                                        'variant_id' => $variation->id,
                                        'amount' => $groupPrice?->price_inc_tax ?? $variation->default_sell_price,
                                        'type' => $groupPrice?->price_type ?? null,
                                        'discounts' => [],
                                    ];
                                }
                            }
                        }
                    }

                    // Find matching discount based on selling_price_group_id
                    $matchingDiscount = $discounts->firstWhere('spg', $group->selling_price_group_id);

                    $discount_amount = null;
                    $discount_type = null;
                    $promotion_amount = [];
                    $promotion_type = 0;

                    if ($matchingDiscount && $currentDate->lte($matchingDiscount->ends_at)) {
                        $promotion_amount = [[
                            'customer_group_id' => $group->customer_group_id,
                            'customer_group_name' => $group->customer_group_name,
                            'selling_price_group_id' => $group->selling_price_group_id,
                            'discount_id' => $matchingDiscount->id,
                            'discount_amount' => $matchingDiscount->discount_amount,
                            'discount_type' => $matchingDiscount->discount_type,
                            'promotion_start' => $matchingDiscount->starts_at,
                            'promotion_end' => $matchingDiscount->ends_at,
                        ]];
                        $promotion_type = $matchingDiscount->discount_type ?? (!empty($variation_prices) ? $variation_prices[0]['type'] : null);
                    }
                    $discount_amount = !empty($variation_prices) ? $variation_prices[0]['amount'] : null;
                    $discount_type = !empty($variation_prices) ? $variation_prices[0]['type'] : null;

                    return [
                        'customer_group_id' => $group->customer_group_id,
                        'customer_group_name' => $group->customer_group_name,
                        'selling_price_group_id' => $group->selling_price_group_id,
                        'discount_amount' => $discount_amount,
                        'discount_type' => $discount_type,
                        'discounts' => $promotion_amount,
                        'promotion_type' => $promotion_type,
                    ];
                })->toArray();

                if ($product->product_variations) {
                    foreach ($product->product_variations as $productVariation) {
                        if ($productVariation->variations) {
                            foreach ($productVariation->variations as $variation) {
                                $variation->variant_id = $variation->id;
                                $variation->amount = $variation->default_sell_price;

                                // Convert quantity in combo_variations to string
                                if (!empty($variation->combo_variations)) {
                                    $variation_ids = array_column($variation->combo_variations, 'variation_id');
                                    $variation->combo_variations = array_map(function ($combo) {
                                        $combo['quantity'] = (string) $combo['quantity'];
                                        return $combo;
                                    }, $variation->combo_variations);
                                    $combo_stock = VariationLocationDetails::whereIn('product_variation_id', $variation_ids)
                                    ->where('location_id', $filters['location_id'])
                                    ->get();
                                    $min_qty_available = $combo_stock->min('qty_available');
                                    $variation->combo_qty = $min_qty_available ?? 0;
                                }
                            }
                        }
                    }
                }

                return $product;
            });
        }


        return $products;
    }
    /**
     * List Variations
     *
     * @urlParam id comma separated ids of variations Example: 2
     * @queryParam product_id Filter by comma separated products ids
     * @queryParam location_id Example: 1
     * @queryParam brand_id
     * @queryParam category_id
     * @queryParam sub_category_id
     * @queryParam not_for_selling Values: 0 or 1
     * @queryParam name Search term for product name
     * @queryParam sku Search term for product sku
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
        "data": [
            {
                "variation_id": 1,
                "variation_name": "",
                "sub_sku": "AS0001",
                "product_id": 1,
                "product_name": "Men's Reverse Fleece Crew",
                "sku": "AS0001",
                "type": "single",
                "business_id": 1,
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "product_image": "1528728059_fleece_crew.jpg",
                "product_description": null,
                "warranty_id": null,
                "brand_id": 1,
                "brand_name": "Levis",
                "unit_id": 1,
                "enable_stock": 1,
                "not_for_selling": 0,
                "unit_name": "Pc(s)",
                "unit_allow_decimal": 0,
                "category_id": 1,
                "category": "Men's",
                "sub_category_id": 5,
                "sub_category": "Shirts",
                "tax_id": 1,
                "tax_type": "exclusive",
                "tax_name": "VAT@10%",
                "tax_amount": 10,
                "product_variation_id": 1,
                "default_purchase_price": "130.0000",
                "dpp_inc_tax": "143.0000",
                "profit_percent": "0.0000",
                "default_sell_price": "130.0000",
                "sell_price_inc_tax": "143.0000",
                "product_variation_name": "",
                "variation_location_details": [],
                "media": [],
                "selling_price_group": [],
                "product_image_url": "http://local.pos.com/uploads/img/1528728059_fleece_crew.jpg",
                "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": null,
                        "is_active": 1,
                        "default_payment_accounts": "",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2019-12-11 04:53:39",
                        "pivot": {
                            "product_id": 1,
                            "location_id": 1
                        }
                    }
                ]
            },
            {
                "variation_id": 2,
                "variation_name": "28",
                "sub_sku": "AS0002-1",
                "product_id": 2,
                "product_name": "Levis Men's Slimmy Fit Jeans",
                "sku": "AS0002",
                "type": "variable",
                "business_id": 1,
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "product_image": "1528727964_levis_jeans.jpg",
                "product_description": null,
                "warranty_id": null,
                "brand_id": 1,
                "brand_name": "Levis",
                "unit_id": 1,
                "enable_stock": 1,
                "not_for_selling": 0,
                "unit_name": "Pc(s)",
                "unit_allow_decimal": 0,
                "category_id": 1,
                "category": "Men's",
                "sub_category_id": 4,
                "sub_category": "Jeans",
                "tax_id": 1,
                "tax_type": "exclusive",
                "tax_name": "VAT@10%",
                "tax_amount": 10,
                "product_variation_id": 2,
                "default_purchase_price": "70.0000",
                "dpp_inc_tax": "77.0000",
                "profit_percent": "0.0000",
                "default_sell_price": "70.0000",
                "sell_price_inc_tax": "77.0000",
                "product_variation_name": "Waist Size",
                "variation_location_details": [
                    {
                        "id": 1,
                        "product_id": 2,
                        "product_variation_id": 2,
                        "variation_id": 2,
                        "location_id": 1,
                        "qty_available": "50.0000",
                        "created_at": "2018-01-06 06:57:11",
                        "updated_at": "2020-08-04 04:11:27"
                    }
                ],
                "media": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "file_name": "1596701997_743693452_test.jpg",
                        "description": null,
                        "uploaded_by": 9,
                        "model_type": "App\\Variation",
                        "woocommerce_media_id": null,
                        "model_id": 2,
                        "created_at": "2020-08-06 13:49:57",
                        "updated_at": "2020-08-06 13:49:57",
                        "display_name": "test.jpg",
                        "display_url": "http://local.pos.com/uploads/media/1596701997_743693452_test.jpg"
                    }
                ],
                "selling_price_group": [],
                "product_image_url": "http://local.pos.com/uploads/img/1528727964_levis_jeans.jpg",
                "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": null,
                        "is_active": 1,
                        "default_payment_accounts": "",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2019-12-11 04:53:39",
                        "pivot": {
                            "product_id": 2,
                            "location_id": 1
                        }
                    }
                ],
                "discounts": [
                    {
                        "id": 2,
                        "name": "FLAT 10%",
                        "business_id": 1,
                        "brand_id": null,
                        "category_id": null,
                        "location_id": 1,
                        "priority": 2,
                        "discount_type": "fixed",
                        "discount_amount": "5.0000",
                        "starts_at": "2021-09-01 11:45:00",
                        "ends_at": "2021-09-30 11:45:00",
                        "is_active": 1,
                        "spg": null,
                        "applicable_in_cg": 1,
                        "created_at": "2021-09-01 11:46:00",
                        "updated_at": "2021-09-01 12:12:55",
                        "formated_starts_at": " 11:45",
                        "formated_ends_at": " 11:45"
                    }
                ]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/variation?page=1",
            "last": null,
            "prev": null,
            "next": "http://local.pos.com/connector/api/variation?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/variation",
            "per_page": "2",
            "to": 2
        }
    }
     */
    public function listVariations($variation_ids = null)
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
            ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
            ->leftjoin('units', 'p.unit_id', '=', 'units.id')
            ->leftjoin('tax_rates as tr', 'p.tax', '=', 'tr.id')
            ->leftjoin('brands', function ($join) {
                $join->on('p.brand_id', '=', 'brands.id')
                    ->whereNull('brands.deleted_at');
            })
            ->leftjoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftjoin('categories as sc', 'p.sub_category_id', '=', 'sc.id')
            ->where('p.business_id', $business_id)
            ->select(
                'variations.id',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.type as type',
                'p.business_id',
                'p.barcode_type',
                'p.expiry_period',
                'p.expiry_period_type',
                'p.enable_sr_no',
                'p.weight',
                'p.product_custom_field1',
                'p.product_custom_field2',
                'p.product_custom_field3',
                'p.product_custom_field4',
                'p.image as product_image',
                'p.product_description',
                'p.warranty_id',
                'p.brand_id',
                'brands.name as brand_name',
                'p.unit_id',
                'p.enable_stock',
                'p.not_for_selling',
                'units.short_name as unit_name',
                'units.allow_decimal as unit_allow_decimal',
                'p.category_id',
                'c.name as category',
                'p.sub_category_id',
                'sc.name as sub_category',
                'p.tax as tax_id',
                'p.tax_type',
                'tr.name as tax_name',
                'tr.amount as tax_amount',
                'variations.product_variation_id',
                'variations.default_purchase_price',
                'variations.dpp_inc_tax',
                'variations.profit_percent',
                'variations.default_sell_price',
                'variations.sell_price_inc_tax',
                'pv.id as product_variation_id',
                'pv.name as product_variation_name'
            );

        $with = [
            'variation_location_details',
            'media',
            'group_prices',
            'product',
            'product.product_locations',
        ];

        if (!empty(request()->input('category_id'))) {
            $query->where('category_id', request()->input('category_id'));
        }

        if (!empty(request()->input('sub_category_id'))) {
            $query->where('p.sub_category_id', request()->input('sub_category_id'));
        }

        if (!empty(request()->input('brand_id'))) {
            $query->where('p.brand_id', request()->input('brand_id'));
        }

        if (request()->has('not_for_selling')) {
            $not_for_selling = request()->input('not_for_selling') == 1 ? 1 : 0;
            $query->where('p.not_for_selling', $not_for_selling);
        }
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        if (!empty(request()->input('location_id'))) {
            $location_id = request()->input('location_id');
            $query->whereHas('product.product_locations', function ($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['variation_location_details'] = function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product.product_locations'] = function ($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        $search = request()->only(['sku', 'name']);

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                if (!empty($search['name'])) {
                    $query->where('p.name', 'like', '%' . $search['name'] . '%');
                }

                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('p.sku', 'like', '%' . $sku . '%')
                        ->where('variations.sub_sku', 'like', '%' . $sku . '%');
                }
            });
        }

        //filter by variations ids
        if (!empty($variation_ids)) {
            $variation_ids = explode(',', $variation_ids);
            $query->whereIn('variations.id', $variation_ids);
        }

        //filter by product ids
        if (!empty(request()->input('product_id'))) {
            $product_ids = explode(',', request()->input('product_id'));
            $query->whereIn('p.id', $product_ids);
        }

        $query->with($with);

        $perPage = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;
        if ($perPage == -1) {
            $variations = $query->get();
        } else {
            //paginate
            $variations = $query->paginate($perPage);
            $variations->appends(request()->query());
        }

        return VariationResource::collection($variations);
    }

    /**
     * List Selling Price Group
     *
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Retail",
                "description": null,
                "business_id": 1,
                "is_active": 1,
                "deleted_at": null,
                "created_at": "2020-10-21 04:30:06",
                "updated_at": "2020-11-16 18:23:15"
            },
            {
                "id": 2,
                "name": "Wholesale",
                "description": null,
                "business_id": 1,
                "is_active": 1,
                "deleted_at": null,
                "created_at": "2020-10-21 04:30:21",
                "updated_at": "2020-11-16 18:23:00"
            }
        ]
    }
     */
    public function getSellingPriceGroup()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->active()
            ->get();

        // Get all products with their variations
        $products = Product::with('variations.group_prices')
            ->where('business_id', $business_id)
            ->get();

        $variation_prices = [];
        foreach ($products as $product) {
            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $variation_prices[$variation->id][$group_price->price_group_id] = [
                        'price' => $group_price->price_inc_tax,
                        'price_type' => $group_price->price_type
                    ];
                }
            }
        }

        // Transform the price groups with variation prices
        $price_groups = $price_groups->map(function ($price_group) use ($variation_prices) {
            $group_variation_prices = [];
            foreach ($variation_prices as $variation_id => $prices) {
                if (isset($prices[$price_group->id])) {
                    $group_variation_prices[$variation_id] = $prices[$price_group->id];
                }
            }

            return [
                'id' => $price_group->id,
                'name' => $price_group->name,
                'business_id' => $price_group->business_id,
                'variation_prices' => $group_variation_prices
            ];
        });

        return CommonResource::collection($price_groups);
    }


    // public function store(Request $request)
    // {
    //     if (!auth()->user()->can('product.create')) {
    //         return response()->json(['error' => 'Unauthorized action.'], 403);
    //     }

    //     try {
    //         $business_id = auth()->user()->business_id;
    //         $form_fields = [
    //             'name',
    //             'brand_id',
    //             'unit_id',
    //             'category_id',
    //             'tax',
    //             'type',
    //             'barcode_type',
    //             'sku',
    //             'alert_quantity',
    //             'tax_type',
    //             'weight',
    //             'product_description',
    //             'sub_unit_ids',
    //             'preparation_time_in_minutes',
    //             'product_custom_field1',
    //             'product_custom_field2',
    //             'enable_stock',
    //             'not_for_selling'
    //         ];

    //         // Extract the form fields from the request
    //         $product_details = $request->only($form_fields);
    //         $product_details['business_id'] = $business_id;
    //         $product_details['created_by'] = auth()->id();

    //         // Set enable stock and not for selling
    //         $product_details['enable_stock'] = $request->input('enable_stock', 0) == 1 ? 1 : 0;
    //         $product_details['not_for_selling'] = $request->input('not_for_selling', 0) == 1 ? 1 : 0;

    //         // Set a default SKU if empty
    //         if (empty($product_details['sku'])) {
    //             $product_details['sku'] = $this->productUtil->generateProductSku();
    //         }

    //         // Begin database transaction
    //         DB::beginTransaction();

    //         // Create the main product record
    //         $product = Product::create($product_details);

    //         // Handle product locations
    //         if ($product_locations = $request->input('product_locations')) {
    //             $product->product_locations()->sync($product_locations);
    //         }

    //         // Handle variable product variations
    //         if ($product->type == 'variable' && !empty($request->input('product_variation'))) {
    //             $input_variations = $request->input('product_variation');

    //             // Create variable product variations with business_id and template name
    //             $variations = $this->productUtil->createVariableProductVariations($product->id, $input_variations);

    //             // Upload variation images for each variation (if needed)
    //             foreach ($variations as $variation) {
    //                 if ($request->hasFile('variation_images')) {
    //                     Media::uploadMedia($product->business_id, $variation, $request, 'variation_images');
    //                 }
    //             }
    //         }

    //         // Handle product image
    //         if ($request->hasFile('image')) {
    //             $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');
    //             $product->image = $product_details['image'];
    //             $product->save();
    //         }

    //         // Handle product brochure
    //         if ($request->hasFile('product_brochure')) {
    //             Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);
    //         }

    //         // Commit transaction
    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'product' => $product,
    //             'message' => __('product.product_added_success')
    //         ], 201);
    //     } catch (\Exception $e) {
    //         
    //         \Log::error('Product Store Error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        // Ensure the authenticated user has the correct permission
        if (!auth()->user()->can('product.create')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            // Get the authenticated user's business ID (from the user, not the session)
            $business_id = auth()->user()->business_id;

            // Define the form fields to capture from the request
            $form_fields = [
                'name',
                'brand_id',
                'unit_id',
                'category_id',
                'tax',
                'type',
                'barcode_type',
                'sku',
                'alert_quantity',
                'tax_type',
                'weight',
                'product_description',
                'sub_unit_ids',
                'preparation_time_in_minutes',
                'product_custom_field1',
                'product_custom_field2',
                'product_custom_field3',
                'product_custom_field4',
                'product_custom_field5',
                'enable_stock',
                'not_for_selling'
            ];

            // Get any additional fields from modules if applicable
            $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            if (!empty($module_form_fields)) {
                $form_fields = array_merge($form_fields, $module_form_fields);
            }

            // Capture product details from the request
            $product_details = $request->only($form_fields);
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = auth()->id(); // Get the creator as the authenticated user

            // Handle stock and not-for-selling logic
            $product_details['enable_stock'] = $request->input('enable_stock', 0) == 1 ? 1 : 0;
            $product_details['not_for_selling'] = $request->input('not_for_selling', 0) == 1 ? 1 : 0;

            // Handle optional fields
            if (!empty($request->input('sub_category_id'))) {
                $product_details['sub_category_id'] = $request->input('sub_category_id');
            }

            if (!empty($request->input('secondary_unit_id'))) {
                $product_details['secondary_unit_id'] = $request->input('secondary_unit_id');
            }

            // Set default SKU if none is provided
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            // Convert alert quantity if present
            if (!empty($product_details['alert_quantity'])) {
                $product_details['alert_quantity'] = $this->productUtil->num_uf($product_details['alert_quantity']);
            }

            // Handle expiry period if stock is enabled
            if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && $product_details['enable_stock'] == 1) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            // Handle serial number enablement
            if (!empty($request->input('enable_sr_no')) && $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1;
            }

            // Start database transaction
            DB::beginTransaction();

            // Create the product
            $product = Product::create($product_details);

            // Fire event for product creation
            event(new ProductsCreatedOrModified($product_details, 'added'));

            // Handle SKU generation if none was provided
            if (empty(trim($request->input('sku')))) {
                $sku = $this->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            // Add product locations if provided
            if ($product_locations = $request->input('product_locations')) {
                $product->product_locations()->sync($product_locations);
            }

            // Handle product types (single, variable, combo)
            if ($product->type == 'single') {
                $this->productUtil->createSingleProductVariation(
                    $product->id,
                    $product->sku,
                    $request->input('single_dpp'),
                    $request->input('single_dpp_inc_tax'),
                    $request->input('profit_percent'),
                    $request->input('single_dsp'),
                    $request->input('single_dsp_inc_tax')
                );
            } elseif ($product->type == 'variable') {
                if (!empty($request->input('product_variation'))) {
                    $input_variations = $request->input('product_variation');
                    $this->createVariableProductVariations($product->id, $input_variations, $business_id);
                }
            } elseif ($product->type == 'combo') {
                // Handle combo product variations
                $combo_variations = [];
                if (!empty($request->input('composition_variation_id'))) {
                    $composition_variation_id = $request->input('composition_variation_id');
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                            'variation_id' => $value,
                            'quantity' => $this->productUtil->num_uf($quantity[$key]),
                            'unit_id' => $unit[$key],
                        ];
                    }
                }

                $this->productUtil->createSingleProductVariation(
                    $product->id,
                    $product->sku,
                    $request->input('item_level_purchase_price_total'),
                    $request->input('purchase_price_inc_tax'),
                    $request->input('profit_percent'),
                    $request->input('selling_price'),
                    $request->input('selling_price_inc_tax'),
                    $combo_variations
                );
            }

            // Add product rack details if provided
            if (!empty($request->input('product_racks'))) {
                $this->productUtil->addRackDetails($business_id, $product->id, $request->input('product_racks'));
            }

            // Handle product image upload (use file upload handler for form-data)
            if ($request->hasFile('image')) {
                $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');
                $product->image = $product_details['image'];
                $product->save();
            }

            // Handle product brochure upload
            if ($request->hasFile('product_brochure')) {
                Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);
            }

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => __('product.product_added_success')
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            

            // Log the error
            \Log::error('Product Store Error: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateProductSku($string)
    {
        // Get the authenticated user's business_id
        $business_id = auth()->user()->business_id;

        // Fetch the SKU prefix for the business
        $sku_prefix = Business::where('id', $business_id)->value('sku_prefix');

        // Generate and return the SKU by appending the formatted string to the prefix
        return $sku_prefix . str_pad($string, 4, '0', STR_PAD_LEFT);
    }


    public function createVariableProductVariations($product, $input_variations, $business_id = null)
    {
        // Check if product is an object, if not, retrieve it by ID
        if (!is_object($product)) {
            $product = Product::find($product);
        }

        // Ensure business ID is provided
        if (is_null($business_id)) {
            throw new \Exception("Business ID is required.");
        }

        foreach ($input_variations as $key => $value) {
            $variation_template_name = !empty($value['name']) ? $value['name'] : null;

            // Ensure template name is present
            if (empty($variation_template_name)) {
                throw new \Exception("Variation template name is required.");
            }

            $variation_template_id = !empty($value['variation_template_id']) ? $value['variation_template_id'] : null;

            // Create or fetch the variation template
            if (empty($variation_template_id)) {
                $variation_template = VariationTemplate::where('business_id', $business_id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($variation_template_name)])
                    ->with('values')
                    ->first();

                if (empty($variation_template)) {
                    $variation_template = VariationTemplate::create([
                        'name' => $variation_template_name,
                        'business_id' => $business_id,
                    ]);
                }
                $variation_template_id = $variation_template->id;
            } else {
                $variation_template = VariationTemplate::with('values')->find($variation_template_id);
            }

            // Create the main product variation record
            $product_variation_data = [
                'name' => $variation_template->name,
                'product_id' => $product->id,
                'is_dummy' => 1,
                'variation_template_id' => $variation_template_id,
            ];
            $product_variation = ProductVariation::create($product_variation_data);

            // Create individual variations within the product variation
            if (!empty($value['variations'])) {
                $variation_data = [];
                $count = Variation::withTrashed()->where('product_id', $product->id)->count() + 1;

                foreach ($value['variations'] as $k => $v) {
                    // Skip hidden variations
                    if (isset($v['is_hidden']) && $v['is_hidden'] == 1) {
                        continue;
                    }

                    $sub_sku = empty($v['sub_sku']) ? $this->generateSubSku($product->sku, $count, $product->barcode_type) : $v['sub_sku'];

                    // Handle variation value
                    $variation_value_id = !empty($v['variation_value_id']) ? $v['variation_value_id'] : null;
                    $variation_value_name = !empty($v['value']) ? $v['value'] : null;

                    if (!empty($variation_value_id)) {
                        $variation_value = $variation_template->values->where('id', $variation_value_id)->first();
                        $variation_value_name = $variation_value->name ?? $variation_value_name;
                    } else {
                        $variation_value = VariationValueTemplate::firstOrCreate(
                            ['name' => $variation_value_name, 'variation_template_id' => $variation_template_id]
                        );
                        $variation_value_id = $variation_value->id;
                    }

                    // Prepare data for each variation
                    $variation_data[] = [
                        'name' => $variation_value_name,
                        'variation_value_id' => $variation_value_id,
                        'product_id' => $product->id,
                        'sub_sku' => $sub_sku,
                        'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                        'dpp_inc_tax' => $this->num_uf($v['dpp_inc_tax']),
                        'profit_percent' => $this->num_uf($v['profit_percent']),
                        'default_sell_price' => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax']),
                    ];
                    $count++;
                }

                // Bulk insert variations for the product variation
                $variations = $product_variation->variations()->createMany($variation_data);

                // Handle image upload for each variation
                foreach ($variations as $i => $variation) {
                    // Dynamically construct the image key based on the form-data naming convention
                    $image_key = "variation_images_{$key}_{$i}";

                    if (request()->hasFile($image_key)) {
                        Media::uploadMedia($product->business_id, $variation, request(), $image_key);
                    }
                }
            }
        }
    }

    public function generateSubSku($sku, $c, $barcode_type)
    {
        $sub_sku = $sku . $c;

        if (in_array($barcode_type, ['C128', 'C39'])) {
            $sub_sku = $sku . '-' . $c;
        }

        return $sub_sku;
    }
    public function num_uf($input_number, $currency_details = null)
    {
        $thousand_separator = '';
        $decimal_separator = '';

        // If there are currency details, use them for formatting
        if (!empty($currency_details)) {
            $thousand_separator = $currency_details->thousand_separator;
            $decimal_separator = $currency_details->decimal_separator;
        } else {
            // Default formatting (you may update this based on your app's settings)
            $thousand_separator = ',';
            $decimal_separator = '.';
        }

        // Remove thousand separators and replace decimal separator with a period
        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float) $num;
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('product.update')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            // Retrieve business ID and product details
            $business_id = auth()->user()->business_id;
            $product_details = $request->only([
                'name',
                'brand_id',
                'unit_id',
                'category_id',
                'tax',
                'barcode_type',
                'sku',
                'alert_quantity',
                'tax_type',
                'weight',
                'product_description',
                'sub_unit_ids',
                'preparation_time_in_minutes',
                'product_custom_field1',
                'product_custom_field2',
                'product_custom_field3',
                'product_custom_field4',
                'product_custom_field5',
                'product_custom_field6',
                'product_custom_field7',
                'product_custom_field8',
                'product_custom_field9',
                'product_custom_field10'
            ]);

            DB::beginTransaction();

            // Fetch the product and update its details
            $product = Product::where('business_id', $business_id)->where('id', $id)->firstOrFail();
            $product->fill($product_details);
            $product->save();

            // Handling variations based on the product type
            if ($product->type == 'single') {
                $single_data = $request->only([
                    'single_variation_id',
                    'single_dpp',
                    'single_dpp_inc_tax',
                    'single_dsp_inc_tax',
                    'profit_percent',
                    'single_dsp'
                ]);
                $variation = Variation::find($single_data['single_variation_id']);

                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($single_data['single_dpp']);
                $variation->dpp_inc_tax = $this->productUtil->num_uf($single_data['single_dpp_inc_tax']);
                $variation->profit_percent = $this->productUtil->num_uf($single_data['profit_percent']);
                $variation->default_sell_price = $this->productUtil->num_uf($single_data['single_dsp']);
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($single_data['single_dsp_inc_tax']);
                $variation->save();

                // Handle media uploads for the single product variation
                Media::uploadMedia($product->business_id, $variation, $request, 'variation_images');
            } elseif ($product->type == 'variable') {
                // Update existing variable variations if any
                if ($input_variations_edit = $request->get('product_variation_edit')) {
                    $this->productUtil->updateVariableProductVariations($product->id, $input_variations_edit);
                }

                // Add new variable variations if any
                if ($input_variations = $request->input('product_variation')) {
                    $this->productUtil->createVariableProductVariations($product->id, $input_variations, $business_id);
                }
            } elseif ($product->type == 'combo') {
                // Handle combo product variations
                $combo_variations = [];
                if ($composition_variation_id = $request->input('composition_variation_id')) {
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                            'variation_id' => $value,
                            'quantity' => $this->productUtil->num_uf($quantity[$key]),
                            'unit_id' => $unit[$key],
                        ];
                    }
                }

                $variation = Variation::find($request->input('combo_variation_id'));
                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($request->input('item_level_purchase_price_total'));
                $variation->dpp_inc_tax = $this->productUtil->num_uf($request->input('purchase_price_inc_tax'));
                $variation->profit_percent = $this->productUtil->num_uf($request->input('profit_percent'));
                $variation->default_sell_price = $this->productUtil->num_uf($request->input('selling_price'));
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($request->input('selling_price_inc_tax'));
                $variation->combo_variations = $combo_variations;
                $variation->save();
            }

            // Commit transaction if no errors occur
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('product.product_updated_success')
            ]);
        } catch (\Exception $e) {
            // Rollback in case of errors
            
            \Log::error("Product Update Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }




    public function deleteMedia($media_id)
    {
        // Check for proper permissions
        if (!auth()->user()->can('product.update')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        try {
            // Get business ID from the authenticated user instead of session
            $business_id = auth()->user()->business_id;

            // Call the deleteMedia method (assumed to exist in the Media model)
            Media::deleteMedia($business_id, $media_id);

            // Success response
            return response()->json([
                'success' => true,
                'message' => __('lang_v1.file_deleted_successfully')
            ], 200);
        } catch (\Exception $e) {
            // Log the error message
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            // Error response
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong')
            ], 500);
        }
    }




    public function view($id)
    {
        // Check for proper permissions
        if (!auth()->user()->can('product.view')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            // Get business ID from the authenticated user, no need for session
            $business_id = auth()->user()->business_id;

            // Fetch the product with related models
            $product = Product::where('business_id', $business_id)
                ->with([
                    'brand',
                    'unit',
                    'category',
                    'sub_category',
                    'product_tax',
                    'variations',
                    'variations.product_variation',
                    'variations.group_prices',
                    'variations.media',
                    'product_locations',
                    'warranty',
                    'media'
                ])
                ->findOrFail($id);

            // Fetch price groups
            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->pluck('name', 'id');

            // Filter allowed price groups based on permissions
            $allowed_group_prices = [];
            foreach ($price_groups as $key => $value) {
                if (auth()->user()->can('selling_price_group.' . $key)) {
                    $allowed_group_prices[$key] = $value;
                }
            }

            // Prepare group price details
            $group_price_details = [];
            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $group_price_details[$variation->id][$group_price->price_group_id] = [
                        'price' => $group_price->price_inc_tax,
                        'price_type' => $group_price->price_type,
                        'calculated_price' => $group_price->calculated_price
                    ];
                }
            }

            // Get rack details using productUtil
            $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

            // Handle combo variations if the product is of type 'combo'
            $combo_variations = [];
            if ($product->type == 'combo') {
                $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $business_id);
            }

            // Return the response as JSON
            return response()->json([
                'product' => $product,
                'rack_details' => $rack_details,
                'allowed_group_prices' => $allowed_group_prices,
                'group_price_details' => $group_price_details,
                'combo_variations' => $combo_variations
            ], 200);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function Dataforcreate()
    {
        try {


            $business_id = auth()->user()->business_id;

            // Check subscription and quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return response()->json(['error' => 'Subscription expired.'], 403);
            } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
                return response()->json(['error' => 'Product quota exceeded.'], 403);
            }

            // Fetch necessary data
            $categories = Category::where('business_id', $business_id)->get();
            $brands = Brands::where('business_id', $business_id)->get();
            $units = Unit::where('business_id', $business_id)->get();
            $taxes = TaxRate::where('business_id', $business_id)->get();

            // Barcode settings
            $barcode_types = $this->barcode_types;
            $barcode_default = $this->productUtil->barcode_default();

            // Default profit percent
            $default_profit_percent = auth()->user()->business->default_profit_percent;

            // Fetch all business locations
            $business_locations = BusinessLocation::where('business_id', $business_id)->get();

            // Handle duplicate product logic
            $duplicate_product = null;
            $rack_details = null;
            $sub_categories = [];

            if (!empty(request()->input('d'))) {
                $duplicate_product = Product::where('business_id', $business_id)->find(request()->input('d'));
                if ($duplicate_product) {
                    $duplicate_product->name .= ' (copy)';

                    // Fetch sub-categories if the duplicate product has a category
                    if (!empty($duplicate_product->category_id)) {
                        $sub_categories = Category::where('business_id', $business_id)
                            ->where('parent_id', $duplicate_product->category_id)
                            ->get();
                    }

                    // Fetch rack details for the product
                    $rack_details = $this->productUtil->getRackDetails($business_id, $duplicate_product->id);
                }
            }

            // Count the number of selling price groups
            $selling_price_group_count = SellingPriceGroup::where('business_id', $business_id)->count();

            // Product types (single, variable, combo)
            $product_types = $this->product_types();

            // Warranties
            $warranties = Warranty::where('business_id', $business_id)->get();

            // Common settings for the business
            $common_settings = auth()->user()->business->common_settings;

            // Return the response as JSON
            return response()->json([
                'categories' => $categories,
                'brands' => $brands,
                'units' => $units,
                'taxes' => $taxes,
                'barcode_types' => $barcode_types,
                'barcode_default' => $barcode_default,
                'default_profit_percent' => $default_profit_percent,
                'business_locations' => $business_locations,
                'duplicate_product' => $duplicate_product,
                'sub_categories' => $sub_categories,
                'rack_details' => $rack_details,
                'selling_price_group_count' => $selling_price_group_count,
                'product_types' => $product_types,
                'warranties' => $warranties,
                'common_settings' => $common_settings
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()  // Add this line to send back the error details
            ], 500);
        }
    }

    private function product_types()
    {
        return [
            'single' => __('lang_v1.single'),
            'variable' => __('lang_v1.variable'),
            'combo' => __('lang_v1.combo'),
        ];
    }

    public function indexProduct()
    {
        $filters = request()->only(['brand_id','location_id', 'per_page']);
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;


        $search = request()->only(['sku', 'name']);
        //order
        $order_by = null;
        $order_direction = null;
        $sellingPriceGroupId = request()->input('customer_group_id');

        if (!empty(request()->input('order_by'))) {
            $order_by = in_array(request()->input('order_by'), ['product_name', 'newest']) ? request()->input('order_by') : null;
            $order_direction = in_array(request()->input('order_direction'), ['asc', 'desc']) ? request()->input('order_direction') : 'asc';
        }

        $products = $this->__getProductsList(3, $filters, $search, true, $order_by, $order_direction);
        return ProductResource::collection($products);
    }

    private function __getProductsList($business_id, $filters = [], $search = [], $pagination = false, $order_by = null, $order_direction = null)
{
    $query = Product::where('business_id', $business_id);

    $with = [
        'media',
        'product_variations.variations.media',
        'product_locations'
    ];

    // Filter conditions
    // if (!empty($filters['category_id'])) {
    //     $category_ids = explode(',', $filters['category_id']);
    //     $query->whereIn('category_id', $category_ids);
    // }

    // if (!empty($filters['sub_category_id'])) {
    //     $sub_category_id = explode(',', $filters['sub_category_id']);
    //     $query->whereIn('sub_category_id', $sub_category_id);
    // }

    if (!empty($filters['brand_id'])) {
        $brand_ids = explode(',', $filters['brand_id']);
        $query->whereIn('brand_id', $brand_ids);
    }

    if (!empty($filters['location_id'])) {
        $location_id = $filters['location_id'];
        $query->whereHas('product_locations', function ($q) use ($location_id) {
            $q->where('product_locations.location_id', $location_id);
        });
    }

    if (!empty($filters['product_ids'])) {
        $query->whereIn('id', $filters['product_ids']);
    }
    $query->where('products.name', 'not like', '%ឡាប៊ែល%');
    $query->where('products.not_for_selling', 0);

    // Search
    if (!empty($search)) {
        $query->where(function ($query) use ($search) {
            if (!empty($search['name'])) {
                $query->where('products.name', 'like', '%' . $search['name'] . '%');
            }

            if (!empty($search['sku'])) {
                $sku = $search['sku'];
                $query->orWhere('sku', 'like', '%' . $sku . '%');
                $query->orWhereHas('variations', function ($q) use ($sku) {
                    $q->where('variations.sub_sku', 'like', '%' . $sku . '%');
                });
            }
        });
    }

    // Order by
    if (!empty($order_by)) {
        if ($order_by == 'product_name') {
            $query->orderBy('products.name', $order_direction);
        } elseif ($order_by == 'newest') {
            $query->orderBy('products.id', $order_direction);
        }
    }

    $query->with($with);

    // Pagination
    $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
    if ($pagination && $perPage != -1) {
        $products = $query->paginate($perPage);
    } else {
        $products = $query->get();
    }

    // Transform products to only include name, sku, price, image,
    if ($pagination && $perPage != -1) {
        $products->getCollection()->transform(function ($product) {
            return $this->formatProductData($product);
        });
    } else {
        $products = $products->map(function ($product) {
            return $this->formatProductData($product);
        });
    }
    

    return $products;
}

    private function formatProductData($product)
    {
        $price = null;
        $image = null;

        // Get price from first variation
        if ($product->product_variations && $product->product_variations->count() > 0) {
            $variation = $product->product_variations->first()->variations->first() ?? null;
            if ($variation) {
                $price = $variation->default_sell_price ?? null;
                // Get image from media
                if ($variation->media && $variation->media->count() > 0) {
                    $image = $variation->media->first()->url ?? null;
                }
            }
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $price,
            'image' => $product->image_url,
            'discount_price'=> null
            
            
        ];
    }
    public function productDetail($product_ids)
    {
        try {            
            $products = Product::where('id', $product_ids)
                ->with([
                    'product_variations.variations.media',
                    'product_variations.variations.variation_location_details',
                    'product_variations.variations.group_prices',
                    'category',
                    'sub_category',
                    'brand',
                    'unit',
                    'product_tax',
                    'product_locations'
                ])
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => PublicProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}