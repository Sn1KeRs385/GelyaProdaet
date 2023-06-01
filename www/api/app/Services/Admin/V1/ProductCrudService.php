<?php

namespace App\Services\Admin\V1;

use App\Models\File;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\User;
use App\Services\Admin\BaseCrudService;
use App\Services\ProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductCrudService extends BaseCrudService
{
    public function __construct(protected ProductService $productService)
    {
    }

    protected array $hasManyRelationMapper = [
        [
            'name' => 'items',
            'crudService' => ProductItemCrudService::class,
            'foreignKey' => 'product_id',
        ]
    ];

    protected function getModelQuery(): Builder
    {
        return Product::query();
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', 'country', 'type', 'files', 'gender', 'items.size', 'items.sizeYear', 'items.color']);
    }

    protected function showAfterQueryExecHook(Model &$model): void
    {
        /** @var Product $model */
//        $model->setAppends([
//            'price_normalize',
//            'price_buy_normalize',
//            'price_sell_normalize',
//            'price_final_normalize'
//        ]);

        $model->files->each(function (File $file) {
            $file->setAppends(['url']);
        });

        $model->items->each(function (ProductItem $item) {
            $item->setAppends([
                'price_normalize',
                'price_buy_normalize',
                'price_sell_normalize',
                'price_final_normalize'
            ]);
        });
    }

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['brand', 'country', 'type', 'gender', 'tgMessages', 'items.size', 'items.sizeYear']);
    }

    protected function indexAfterPaginateHook(LengthAwarePaginator|Collection &$paginate): void
    {
        $paginate->each(function (Product $product) {
            $product->setAppends(['is_send_to_telegram']);
        });
    }

    protected function storeDataHook(array &$data): void
    {
        $this->storeUpdateDataHook($data);
    }

    protected function updateDataHook(array &$data): void
    {
        $this->storeUpdateDataHook($data);
    }

    protected function storeUpdateDataHook(array &$data): void
    {
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                $item['price'] = $item['price_normalize'] ?? $data['price_normalize'] ?? null;
                $item['price_buy'] = $item['price_buy_normalize'] ?? $data['price_buy_normalize'] ?? null;
                $item['price_final'] = $item['price_final_normalize'] ?? $data['price_final_normalize'] ?? null;

                if ($item['price']) {
                    $item['price'] = $item['price'] * 100;
                }
                if ($item['price_buy']) {
                    $item['price_buy'] = $item['price_buy'] * 100;
                }
                if ($item['price_final']) {
                    $item['price_final'] = $item['price_final'] * 100;
                }
            }
        }
    }

    public function resendToTelegram(string $id): Product
    {
        $product = Product::findOrFail($id);

        return $this->productService->resendToTelegram($product);
    }

    public function sendToUserTelegram(User $user, string $productId): Product
    {
        $product = Product::findOrFail($productId);

        return $this->productService->sendToUserTelegram($product, $user);
    }
}
