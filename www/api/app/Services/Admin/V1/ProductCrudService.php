<?php

namespace App\Services\Admin\V1;

use App\Models\File;
use App\Models\Product;
use App\Models\ProductItem;
use App\Services\Admin\BaseCrudService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductCrudService extends BaseCrudService
{
    protected array $relationMapper = [
        [
            'name' => 'items',
            'crudService' => ProductItemCrudService::class,
            'foreignKey' => 'product_id',
        ]
    ];

    protected function getModelQuery(): Builder
    {
        return Product::query();
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', 'country', 'type', 'files', 'gender', 'items.size', 'items.color']);
    }

    protected function showAfterQueryExecHook(Model &$model): void
    {
        /** @var Product $model */
        $model->items->each(function (ProductItem $item) {
            $item->setAppends(['price_normalize', 'price_buy_normalize']);
        });

        $model->files->each(function (File $file) {
            $file->setAppends(['url']);
        });
    }

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', 'country', 'type', 'gender']);
    }

//    protected function indexAfterPaginateHook(LengthAwarePaginator|Collection &$paginate): void
//    {
//        $paginate->each(function (Product $product) {
//            $product->items->each(function (ProductItem $item) {
//                $item->setAppends(['price_normalize', 'price_buy_normalize']);
//            });
//        });
//    }

    protected function storeDataHook(array &$data): void
    {
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                $item['price'] = $item['price'] ?? $data['price'] ?? null;
                $item['price_buy'] = $item['price_buy'] ?? $data['price_buy'] ?? null;

                if ($item['price']) {
                    $item['price'] = $item['price'] * 100;
                }
                if ($item['price_buy']) {
                    $item['price_buy'] = $item['price_buy'] * 100;
                }
            }
        }
    }
}
