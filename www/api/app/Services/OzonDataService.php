<?php

namespace App\Services;

use App\Models\OzonData;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Ozon\Dto\Attribute;
use Modules\Ozon\Dto\AttributeValue;
use Modules\Ozon\Dto\Category;
use Modules\Ozon\Services\OzonApiService;
use Spatie\LaravelData\DataCollection;

class OzonDataService
{
    public function __construct(protected OzonApiService $ozonApiService)
    {
    }

    /**
     * @return Collection<int, Category>|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->ozonApiService->getCategoryTree()->result->toCollection();
    }

    /**
     * @param  int  $categoryId
     * @return Collection<int, Attribute>|Attribute[]
     */
    public function getAttributesByCategoryId(int $categoryId): Collection
    {
        return Cache::tags(config('cache.config.ozonData.tag'))
            ->remember(
                config('cache.config.ozonData.attribute.key') . "_{$categoryId}",
                config('cache.config.ozonData.attributeValues.ttl'),
                function () use ($categoryId) {
                    return $this->ozonApiService->getAttributes([$categoryId])->result[0]->attributes->toCollection();
                }
            );
    }

    /**
     * @param  int  $categoryId
     * @param  int  $attributeId
     * @return Collection<int, AttributeValue>|AttributeValue[]
     */
    public function getAttributeValues(int $categoryId, int $attributeId): Collection
    {
        ini_set('memory_limit', '4G');

        return Cache::tags(config('cache.config.ozonData.tag'))
            ->remember(
                config('cache.config.ozonData.attributeValues.key') . "_{$categoryId}_{$attributeId}",
                config('cache.config.ozonData.attributeValues.ttl'),
                function () use ($categoryId, $attributeId) {
                    /** @var Collection $items */
                    $items = null;
                    $lastId = null;
                    do {
                        $response = $this->ozonApiService->getAttributeValues($categoryId, $attributeId, 5000, $lastId);
                        $lastId = $response->result[$response->result->count() - 1]->id;
                        if (!$items) {
                            $items = $response->result->toCollection();
                        } else {
                            /** @var Collection $temp */
                            $temp = $response->result->toCollection();

                            $items = $items->merge($temp);
                        }
                    } while ($response->has_next);

                    return $items;
                }
            );
    }
}
