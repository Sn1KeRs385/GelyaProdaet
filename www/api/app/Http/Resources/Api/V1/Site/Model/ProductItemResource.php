<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\ProductItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductItem
 */
class ProductItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'price_normalize' => $this->priceNormalize,
            'price_final' => $this->price_final,
            'price_final_normalize' => $this->priceFinalNormalize,
            'is_sold' => $this->is_sold,
            'count' => $this->count,
            'is_reserved' => $this->is_reserved,
            'size' => $this->size ? ListOptionResource::make($this->size) : null,
            'size_year' => $this->sizeYear ? ListOptionResource::make($this->sizeYear) : null,
            'color' => $this->color ? ListOptionResource::make($this->color) : null,
        ];
    }
}
