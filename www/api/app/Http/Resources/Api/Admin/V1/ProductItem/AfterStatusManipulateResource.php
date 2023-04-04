<?php

namespace App\Http\Resources\Api\Admin\V1\ProductItem;

use App\Models\ProductItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductItem
 */
class AfterStatusManipulateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            ...$this->getAttributes(),
            'price_buy_normalize' => $this->priceBuyNormalize,
            'price_normalize' => $this->priceNormalize,
            'price_sell_normalize' => $this->priceSellNormalize,
        ];
    }
}
