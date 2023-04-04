<?php

namespace App\Services;

use App\Exceptions\Models\ProductItem\CanNotChangePriceSellException;
use App\Exceptions\Models\ProductItem\CanNotMarkNotForSaleException;
use App\Exceptions\Models\ProductItem\CanNotMarkSoldException;
use App\Exceptions\Models\ProductItem\CanNotRollbackForSaleStatusException;
use App\Models\ProductItem;

class ProductItemService
{
    public function markSold(ProductItem $productItem, int $priceSell = null): ProductItem
    {
        if (!$productItem->is_for_sale || $productItem->is_sold) {
            throw new CanNotMarkSoldException();
        }

        $productItem->is_sold = true;
        $productItem->price_sell = $priceSell ?? $productItem->price;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }

    public function changePriceSell(ProductItem $productItem, int $priceSell): ProductItem
    {
        if (!$productItem->is_sold) {
            throw new CanNotChangePriceSellException();
        }

        $productItem->is_sold = true;
        $productItem->price_sell = $priceSell ?? $productItem->price;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }

    public function markNotForSale(ProductItem $productItem): ProductItem
    {
        if (!$productItem->is_for_sale || $productItem->is_sold) {
            throw new CanNotMarkNotForSaleException();
        }

        $productItem->is_for_sale = false;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }

    public function rollbackForSaleStatus(ProductItem $productItem): ProductItem
    {
        if ($productItem->is_for_sale && !$productItem->is_sold) {
            throw new CanNotRollbackForSaleStatusException();
        }

        $productItem->is_sold = false;
        $productItem->is_for_sale = true;
        $productItem->price_sell = null;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }
}
