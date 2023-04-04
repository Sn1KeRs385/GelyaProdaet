<?php

namespace App\Models\Traits\Attributes;

/**
 * @property int|null $price
 * @property int|null $priceBuy
 * @property int|null $priceSell
 * @property float|null $priceNormalize
 * @property float|null $priceBuyNormalize
 * @property float|null $priceSellNormalize
 * @property bool $isSendToTelegram
 */
trait ProductAttributes
{
    public function getPriceAttribute(): int|null
    {
        return $this->items[0]?->price ?? null;
    }

    public function getPriceBuyAttribute(): int|null
    {
        return $this->items[0]?->price_buy ?? null;
    }

    public function getPriceSellAttribute(): int|null
    {
        return $this->items[0]?->price_sell ?? null;
    }

    public function getPriceNormalizeAttribute(): float|null
    {
        if (!$this->price) {
            return null;
        }

        return $this->price / 100;
    }

    public function getPriceBuyNormalizeAttribute(): float|null
    {
        if (!$this->priceBuy) {
            return null;
        }

        return $this->priceBuy / 100;
    }

    public function getPriceSellNormalizeAttribute(): float|null
    {
        if (!$this->priceSell) {
            return null;
        }

        return $this->priceSell / 100;
    }

    public function getIsSendToTelegramAttribute(): bool
    {
        return !!$this->tgMessages->where('is_forward_error', '=', false)->first();
    }
}
