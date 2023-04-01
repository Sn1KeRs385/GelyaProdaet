<?php

namespace App\Services\Admin\V1;

use App\Exceptions\CanNotMarkNotForSaleProductItemException;
use App\Exceptions\CanNotMarkSoldProductItemException;
use App\Exceptions\CanNotRollbackForSaleStatusProductItemException;
use App\Models\ProductItem;
use App\Services\Admin\BaseCrudService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductItemCrudService extends BaseCrudService
{
    protected function getModelQuery(): Builder
    {
        return ProductItem::query();
    }

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['color', 'size']);
    }

    protected function indexAfterPaginateHook(LengthAwarePaginator &$paginate): void
    {
        $paginate->each(function (ProductItem $item) {
            $item->setAppends(['price_normalize', 'price_buy_normalize']);
        });
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['size', 'color', 'product']);
    }

    public function markSold(string $id): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);

        if (!$productItem->is_for_sale || $productItem->is_sold) {
            throw new CanNotMarkSoldProductItemException();
        }

        $productItem->is_sold = true;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }

    public function markNotForSale(string $id): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);

        if (!$productItem->is_for_sale || $productItem->is_sold) {
            throw new CanNotMarkNotForSaleProductItemException();
        }

        $productItem->is_for_sale = false;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }

    public function rollbackForSaleStatus(string $id): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);

        if ($productItem->is_for_sale && !$productItem->is_sold) {
            throw new CanNotRollbackForSaleStatusProductItemException();
        }

        $productItem->is_sold = false;
        $productItem->is_for_sale = true;
        $productItem->save();
        $productItem->product->touch();
        return $productItem;
    }
}
