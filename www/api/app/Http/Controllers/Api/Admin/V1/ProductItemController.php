<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Http\Requests\Api\Admin\V1\ProductItem\ChangePriceSellRequest;
use App\Http\Requests\Api\Admin\V1\ProductItem\MarkSoldRequest;
use App\Http\Resources\Api\Admin\V1\ProductItem\AfterStatusManipulateResource;
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

    public function markSold(string $id, MarkSoldRequest $request): \Illuminate\Http\JsonResponse
    {
        $priceSell = $request->price_sell;
        if ($priceSell) {
            $priceSell = (int)round($priceSell * 100);
        }

        return response()->json(
            AfterStatusManipulateResource::make($this->crudService->markSold($id, $priceSell)),
            Response::HTTP_OK
        );
    }

    public function changePriceSell(string $id, ChangePriceSellRequest $request): \Illuminate\Http\JsonResponse
    {
        $priceSell = (int)round($request->price_sell * 100);

        return response()->json(
            AfterStatusManipulateResource::make($this->crudService->changePriceSell($id, $priceSell)),
            Response::HTTP_OK
        );
    }

    public function markNotForSale(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            AfterStatusManipulateResource::make($this->crudService->markNotForSale($id)),
            Response::HTTP_OK
        );
    }

    public function rollbackForSaleStatus(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            AfterStatusManipulateResource::make($this->crudService->rollbackForSaleStatus($id)),
            Response::HTTP_OK
        );
    }

    public function switchReserve(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            AfterStatusManipulateResource::make($this->crudService->switchReserve($id)),
            Response::HTTP_OK
        );
    }
}
