<?php

namespace App\Services\Admin\V1;

use App\Models\Product;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
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

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', "country"]);
    }

    protected function storeDataHook(array &$data): void
    {
        if (isset($data['relation_items'])) {
            foreach ($data['relation_items'] as &$item) {
                $item['price'] = $item['price'] ?? $data['price'] ?? null;
            }
        }
    }
}
