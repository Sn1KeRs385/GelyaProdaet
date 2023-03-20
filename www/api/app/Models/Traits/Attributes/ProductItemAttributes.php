<?php

namespace App\Models\Traits\Attributes;

/**
 * @property float $priceNormalize
 * @property float $priceBuyNormalize
 */
trait ProductItemAttributes
{
    public function getPriceNormalizeAttribute(): float
    {
        return $this->price / 100;
    }

    public function getPriceBuyNormalizeAttribute(): float
    {
        return $this->price_buy / 100;
    }
}
