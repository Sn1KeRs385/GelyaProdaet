<?php

namespace App\Models\Traits\Attributes;

use App\Models\ProductItem;

/**
 * @property int|null $price
 * @property int|null $priceFinal
 * @property int|null $priceBuy
 * @property int|null $priceSell
 * @property float|null $priceNormalize
 * @property float|null $priceFinalNormalize
 * @property float|null $priceBuyNormalize
 * @property float|null $priceSellNormalize
 * @property bool $isSameCostItems
 * @property bool $isSendToTelegram
 */
trait ProductAttributes
{
    public function getPriceAttribute(): int|null
    {
        return $this->items[0]?->price ?? null;
    }

    public function getPriceFinalAttribute(): int|null
    {
        return $this->items[0]?->price_final ?? null;
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

    public function getPriceFinalNormalizeAttribute(): float|null
    {
        if (!$this->priceFinal) {
            return null;
        }

        return $this->priceFinal / 100;
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

    public function getIsSameCostItemsAttribute(): bool
    {
        $items = $this->items->filter(fn(ProductItem $item) => $item->is_for_sale)->values();

        foreach ($items as $item) {
            /** @var ProductItem $item */
            if ($item->price !== $this->price || $item->price_final !== $this->priceFinal) {
                return false;
            }
        }

        return true;
    }

    public function getIsSendToTelegramAttribute(): bool
    {
        return !!$this->tgMessages->where('is_not_found_error', '=', false)->first();
    }
}
