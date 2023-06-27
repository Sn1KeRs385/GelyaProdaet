<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\IndexRequest;
use App\Http\Resources\Api\V1\Site\Model\ProductResource;
use App\Repositories\ProductRepository;
use App\Utils\ResponseFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected ResponseFormatter $responseFormatter
    ) {
    }

    public function index(IndexRequest $request): JsonResponse
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 25;

        return Cache::tags(config('cache.config.api.v1.products.tag'))
            ->remember(
                config('cache.config.api.v1.products.index.key') . ":Pg:{$page}:Per:{$perPage}",
                config('cache.config.api.v1.products.index.ttl'),
                function () use ($page, $perPage) {
                    $products = $this->productRepository->paginateListForSite($perPage, $page);

                    return response()->json(
                        $this->responseFormatter->formatPaginator($products, ProductResource::class),
                        Response::HTTP_OK
                    );
                }
            );
    }
}
