<?php

namespace Modules\Ozon\Services;

use GuzzleHttp\Client as GuzzleClient;
use Modules\Ozon\Dto\Responses\AttributesResponse;
use Modules\Ozon\Dto\Responses\AttributeValuesResponse;
use Modules\Ozon\Dto\Responses\CategoryTreeResponse;
use Modules\Ozon\Dto\Responses\ProductImportInfoResponse;
use Modules\Ozon\Dto\Responses\ProductImportResponse;

class OzonApiService
{
    protected GuzzleClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new GuzzleClient([
            'base_uri' => config('ozon.url.base'),
            'headers' => [
                'Client-Id' => config('ozon.client_id'),
                'Api-Key' => config('ozon.api_key'),
            ],
        ]);
    }

    public function getCategoryTree(): CategoryTreeResponse
    {
        $response = $this->apiClient->request(
            'POST',
            config('ozon.url.category.tree'),
        );

        return CategoryTreeResponse::from(json_decode($response->getBody()->getContents(), true));
    }

    public function getAttributes(array $categoryIds): AttributesResponse
    {
        $response = $this->apiClient->request(
            'POST',
            config('ozon.url.category.attributes'),
            [
                'json' => [
                    'category_id' => $categoryIds,
                ],
            ]
        );

        return AttributesResponse::from(json_decode($response->getBody()->getContents(), true));
    }

    public function getAttributeValues(
        int $categoryId,
        int $attributeId,
        int $limit = 5000,
        int $lastId = null
    ): AttributeValuesResponse {
        $options = [
            'json' => [
                'category_id' => $categoryId,
                'attribute_id' => $attributeId,
                'limit' => $limit,
            ],
        ];

        if ($lastId) {
            $options['json']['last_value_id'] = $lastId;
        }

        $response = $this->apiClient->request(
            'POST',
            config('ozon.url.category.attribute_values'),
            $options
        );

        return AttributeValuesResponse::from(json_decode($response->getBody()->getContents(), true));
    }

    public function importProducts(array $items): ProductImportResponse
    {
        $response = $this->apiClient->request(
            'POST',
            config('ozon.url.product.import'),
            [
                'json' => [
                    'items' => $items,
                ],
            ]
        );

        return ProductImportResponse::from(json_decode($response->getBody()->getContents(), true));
    }

    public function importProductsInfo(int $taskId): ProductImportInfoResponse
    {
        $response = $this->apiClient->request(
            'POST',
            config('ozon.url.product.info'),
            [
                'json' => [
                    'task_id' => $taskId,
                ],
            ]
        );

        return ProductImportInfoResponse::from(json_decode($response->getBody()->getContents(), true));
    }
}
