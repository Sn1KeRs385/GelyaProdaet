<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Services\Admin\V1\ProductCrudService;
use Illuminate\Http\Response;

class ProductController extends BaseCrudController
{
    public function __construct()
    {
        $this->crudService = self::getCrudService();
    }

    protected static function getCrudService(): ProductCrudService
    {
        return app(ProductCrudService::class);
    }

    public function resendToTelegram(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->crudService->resendToTelegram($id),
            Response::HTTP_OK
        );
    }
}
