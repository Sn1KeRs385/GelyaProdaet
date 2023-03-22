<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Services\Admin\V1\ProductItemCrudService;
use Illuminate\Http\Response;

class ProductItemController extends BaseCrudController
{
    public function __construct()
    {
        /** @var ProductItemCrudService $this- >crudService */
        $this->crudService = self::getCrudService();
    }

    protected static function getCrudService(): ProductItemCrudService
    {
        return app(ProductItemCrudService::class);
    }

    public function markSold(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->crudService->markSold($id), Response::HTTP_OK);
    }

    public function markNotForSale(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->crudService->markNotForSale($id), Response::HTTP_OK);
    }

    public function rollbackForSaleStatus(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->crudService->rollbackForSaleStatus($id), Response::HTTP_OK);
    }
}
