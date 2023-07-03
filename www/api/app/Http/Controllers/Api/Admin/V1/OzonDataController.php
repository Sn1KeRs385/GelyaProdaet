<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Services\Admin\V1\OzonDataService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class OzonDataController extends BaseCrudController
{
    public function __construct()
    {
        $this->crudService = self::getCrudService();
    }

    protected static function getCrudService(): OzonDataService
    {
        return app(OzonDataService::class);
    }

    public function getByProductId(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->crudService->getByProductId($id),
            Response::HTTP_OK
        );
    }

    public function getCategories(): \Illuminate\Http\JsonResponse
    {
        return Cache::tags(config('cache.config.admin.v1.ozonData.tag'))
            ->remember(
                config('cache.config.admin.v1.ozonData.categories.key'),
                config('cache.config.admin.v1.ozonData.categories.ttl'),
                function () {
                    return response()->json(
                        $this->crudService->getCategories(),
                        Response::HTTP_OK
                    );
                }
            );
    }

    public function getAttributesByCategoryId(string $id): \Illuminate\Http\JsonResponse
    {
        return Cache::tags(config('cache.config.admin.v1.ozonData.tag'))
            ->remember(
                config('cache.config.admin.v1.ozonData.attributes.key') . "_{$id}",
                config('cache.config.admin.v1.ozonData.attributes.ttl'),
                function () use ($id) {
                    return response()->json(
                        $this->crudService->getAttributesByCategoryId($id),
                        Response::HTTP_OK
                    );
                }
            );
    }

    public function getAttributeValues(string $categoryId, string $attributeId): \Illuminate\Http\JsonResponse
    {
        ini_set('memory_limit', '4G');
        return Cache::tags(config('cache.config.admin.v1.ozonData.tag'))
            ->remember(
                config('cache.config.admin.v1.ozonData.attribute_values.key') . "_{$categoryId}_{$attributeId}",
                config('cache.config.admin.v1.ozonData.attribute_values.ttl'),
                function () use ($categoryId, $attributeId) {
                    return response()->json(
                        $this->crudService->getAttributeValues($categoryId, $attributeId),
                        Response::HTTP_OK
                    );
                }
            );
    }
}
