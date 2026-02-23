<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->product_description,
            'image_url' => $this->image_url,
            'type' => $this->type,
            'enable_stock' => $this->enable_stock,
            'is_inactive' => $this->is_inactive,
            'not_for_selling' => $this->not_for_selling,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'sub_category' => [
                'id' => $this->sub_category?->id,
                'name' => $this->sub_category?->name,
            ],
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
            ],
            'unit' => [
                'id' => $this->unit?->id,
                'name' => $this->unit?->name,
            ],
            'tax' => [
                'id' => $this->product_tax?->id,
                'name' => $this->product_tax?->name,
                'amount' => $this->product_tax?->amount,
            ],
            'locations' => $this->getLocations(),
            'customer_groups' => $this->getCustomerGroups(),
            'variations' => $this->getVariations(),
        ];
    }

    private function getLocations()
    {
        $locations = [];

        if ($this->product_locations) {
            foreach ($this->product_locations as $location) {
                $locations[] = [
                    'id' => $location->id,
                    'location_id' => $location->location_id,
                    'name' => $location->name,
                    'city' => $location->city,
                    'state' => $location->state,
                    'mobile' => $location->mobile,
                    'email' => $location->email,
                ];
            }
        }

        return $locations;
    }

    private function getCustomerGroups()
    {
        $groups = [];

        if ($this->customer_groups && is_array($this->customer_groups)) {
            foreach ($this->customer_groups as $group) {
                $groups[] = [
                    'customer_group_id' => $group['customer_group_id'],
                    'customer_group_name' => $group['customer_group_name'],
                    'selling_price_group_id' => $group['selling_price_group_id'],
                    'discount_amount' => $group['discount_amount'],
                    'discount_type' => $group['discount_type'],
                    'promotion_type' => $group['promotion_type'] ?? 0,
                ];
            }
        }

        return $groups;
    }

    private function getVariations()
    {
        $variations = [];

        if ($this->product_variations) {
            foreach ($this->product_variations as $productVariation) {
                if ($productVariation->variations) {
                    foreach ($productVariation->variations as $variation) {
                        $variations[] = [
                            'id' => $variation->id,
                            'product_variation_id' => $variation->product_variation_id,
                            'name' => $variation->name,
                            'sub_sku' => $variation->sub_sku,
                            'barcode_type' => $this->barcode_type ?? 'EAN13',
                            'default_purchase_price' => $variation->default_purchase_price,
                            'dpp_inc_tax' => $variation->dpp_inc_tax,
                            'default_sell_price' => $variation->default_sell_price,
                            'discount_price'=> null,
                            'sell_price_inc_tax' => $variation->sell_price_inc_tax,
                            'profit_percent' => $variation->profit_percent,
                            'images' => $this->getImages($variation),
                            'group_prices' => $this->getGroupPrices($variation),
                            'location_stock' => $this->getLocationStock($variation),
                        ];
                    }
                }
            }
        }

        return $variations;
    }

    private function getImages($variation)
    {
        $images = [];

        if ($variation->media && $variation->media->count() > 0) {
            foreach ($variation->media as $media) {
                $images[] = [
                    'id' => $media->id,
                    'display_name' => $media->display_name,
                    'display_url' => $media->display_url,
                ];
            }
        }

        return $images;
    }

    private function getGroupPrices($variation)
    {
        $prices = [];

        if ($variation->selling_price_group && $variation->selling_price_group->count() > 0) {
            foreach ($variation->selling_price_group as $groupPrice) {
                $prices[] = [
                    'id' => $groupPrice->id,
                    'price_group_id' => $groupPrice->price_group_id,
                    'price_inc_tax' => $groupPrice->price_inc_tax,
                    'price_type' => $groupPrice->price_type,
                ];
            }
        }

        return $prices;
    }

    private function getLocationStock($variation)
    {
        $stock = [];

        if ($variation->variation_location_details && $variation->variation_location_details->count() > 0) {
            foreach ($variation->variation_location_details as $locDetail) {
                $stock[] = [
                    'id' => $locDetail->id,
                    'location_id' => $locDetail->location_id,
                    'qty_available' => (float)$locDetail->qty_available,
                    'updated_at' => $locDetail->updated_at,
                ];
            }
        }

        return $stock;
    }
}