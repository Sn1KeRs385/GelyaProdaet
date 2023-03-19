<?php

namespace App\Services\Admin\V1;

use App\Models\Product;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;

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

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', 'country', 'type']);
    }

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
