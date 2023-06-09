<?php

namespace App\Models\Traits\Attributes;

/**
 * @property float $priceNormalize
 * @property float $priceBuyNormalize
 * @property float|null $priceFinalNormalize
 * @property float|null $priceSellNormalize
 * @property int|null $earn
 * @property float|null $earnNormalize
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

    public function getPriceFinalNormalizeAttribute(): ?float
    {
        if (!$this->price_final) {
            return null;
        }

        return $this->price_final / 100;
    }

    public function getPriceSellNormalizeAttribute(): ?float
    {
        if (!$this->price_sell) {
            return null;
        }

        return $this->price_sell / 100;
    }

    public function getEarnAttribute(): ?int
    {
        if (!$this->price_sell) {
            return null;
        }

        return $this->price_sell - $this->price_buy;
    }

    public function getEarnNormalizeAttribute(): ?float
    {
        if (!$this->earn) {
            return null;
        }

        return $this->earn / 100;
    }
}
