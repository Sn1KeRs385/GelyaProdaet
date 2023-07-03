<?php

namespace App\Services\Admin\V1;

use App\Models\OzonData;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Ozon\Dto\Attribute;
use Modules\Ozon\Services\OzonApiService;

class OzonDataService extends BaseCrudService
{
    public function __construct(protected \App\Services\OzonDataService $ozonDataService)
    {
    }

    protected function getModelQuery(): Builder
    {
        return OzonData::query();
    }

    public function getByProductId(string $id): ?OzonData
    {
        return $this->getModelQuery()
            ->where('product_id', $id)
            ->first();
    }

    public function getCategories(): Collection
    {
        return $this->ozonDataService->getCategories();
    }

    public function getAttributesByCategoryId(int $categoryId): Collection
    {
        $excludeAttributeIds = array_keys(config('ozon.attribute_bindings'));

        $attributes = $this->ozonDataService->getAttributesByCategoryId($categoryId);

        return $attributes->filter(fn(Attribute $attribute) => !in_array($attribute->id, $excludeAttributeIds))
            ->values();
    }

    public function getAttributeValues(int $categoryId, int $attributeId): Collection
    {
        return $this->ozonDataService->getAttributeValues($categoryId, $attributeId);
    }
}
