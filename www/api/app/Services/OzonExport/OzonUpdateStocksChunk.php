<?php

namespace App\Services\OzonExport;

use App\Models\OzonProduct;
use Modules\Ozon\Dto\Responses\ProductImportStocksResponse;
use Modules\Ozon\Services\OzonApiService;

class OzonUpdateStocksChunk
{
    protected OzonApiService $ozonApiService;
    protected ?ProductImportStocksResponse $response = null;

    protected array $items = [];

    public function __construct()
    {
        $this->ozonApiService = app(OzonApiService::class);
    }

    public function addProductItem(OzonProduct $product): bool|null
    {
        if (!$product->external_id) {
            return null;
        }

        if (count($this->items) >= 100) {
            return false;
        }

        $this->handleProductItem($product);

        return true;
    }

    protected function handleProductItem(OzonProduct $product): void
    {
        $item = [
            'offer_id' => (string)$product->id,
            'product_id' => $product->external_id,
            'stock' => $this->getStocks($product),
        ];

        $this->items[] = $item;
    }

    protected function getStocks(OzonProduct $product): int
    {
        return $product->getProductItemsQuery()
            ->where('is_sold', false)
            ->where('is_for_sale', true)
            ->where('is_reserved', false)
            ->count();
    }

    public function startUpdate(): ?ProductImportStocksResponse
    {
        if ($this->response) {
            return $this->response;
        }

        if (count($this->items) === 0) {
            return null;
        }

        $this->response = $this->ozonApiService->importProductStocks($this->items);

        return $this->response;
    }
}
