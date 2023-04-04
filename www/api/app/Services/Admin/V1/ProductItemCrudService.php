<?php

namespace App\Services\Admin\V1;

use App\Models\ProductItem;
use App\Services\Admin\BaseCrudService;
use App\Services\ProductItemService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductItemCrudService extends BaseCrudService
{
    public function __construct(protected ProductItemService $productItemService)
    {
    }

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
            $item->setAppends(['price_buy_normalize', 'price_normalize', 'price_sell_normalize']);
        });
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['size', 'color', 'product']);
    }

    public function markSold(string $id, int $priceSell = null): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);
        return $this->productItemService->markSold($productItem, $priceSell);
    }

    public function changePriceSell(string $id, int $priceSell): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);
        return $this->productItemService->changePriceSell($productItem, $priceSell);
    }

    public function markNotForSale(string $id): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);
        return $this->productItemService->markNotForSale($productItem);
    }

    public function rollbackForSaleStatus(string $id): ProductItem
    {
        $productItem = ProductItem::findOrFail($id);
        return $this->productItemService->rollbackForSaleStatus($productItem);
    }
}
