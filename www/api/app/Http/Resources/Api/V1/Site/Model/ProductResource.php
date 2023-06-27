<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'price' => $this->price,
            'price_normalize' => $this->priceNormalize,
            'price_final' => $this->priceFinal,
            'price_final_normalize' => $this->priceFinalNormalize,
            'is_same_cost_items' => $this->isSameCostItems,
            'type' => ListOptionResource::make($this->type),
            'gender' => ListOptionResource::make($this->gender),
            'brand' => $this->brand ? ListOptionResource::make($this->brand) : null,
            'country' => $this->country ? ListOptionResource::make($this->country) : null,
            'items' => ProductItemResource::collection($this->items),
        ];

        if ($this->relationLoaded('files')) {
            $data['files'] = FileResource::collection($this->files);
        }

        return $data;
    }
}
