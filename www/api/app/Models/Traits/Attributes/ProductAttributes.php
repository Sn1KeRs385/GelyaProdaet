<?php

namespace App\Models\Traits\Attributes;

/**
 * @property int|null $price
 * @property int|null $priceBuy
 * @property float|null $priceNormalize
 * @property float|null $priceBuyNormalize
 * @property bool $is_send_to_telegram
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

    public function getPriceNormalizeAttribute(): float|null
    {
        if (!$this->price) {
            return null;
        }

        return $this->price / 100;
    }

    public function getPriceBuyNormalizeAttribute(): float|null
    {
        if (!$this->price) {
            return null;
        }

        return $this->priceBuy / 100;
    }

    public function getIsSendToTelegramAttribute(): bool
    {
        return !!$this->tgMessages->where('is_forward_error', '=', false)->first();
    }
}
