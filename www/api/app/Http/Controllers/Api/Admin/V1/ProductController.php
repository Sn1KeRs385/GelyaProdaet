<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Models\User;
use App\Services\Admin\V1\ProductCrudService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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

    public function sendToMyTelegram(string $id): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json(
            $this->crudService->sendToUserTelegram($user, $id),
            Response::HTTP_OK
        );
    }
}
