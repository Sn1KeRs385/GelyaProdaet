<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Console\Command;

class ProductTelegramUpdateAll extends Command
{
    protected const MAX_TRY_COUNT = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:tg-update-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all messages in telegram';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Product::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->chunk(500, function ($products) {
                /** @var Product[] $products */
                foreach ($products as $product) {
                    $this->trySend($product);
                    sleep(2);
                }
            });
    }

    protected function trySend(Product $product, int $try = 1)
    {
        if ($try > self::MAX_TRY_COUNT) {
            return;
        }

        /** @var ProductService $productService */
        $productService = app(ProductService::class);

        try {
            $productService->sendProductToTelegram($product);
        } catch (\Throwable $ex) {
            dump("Product {$product->id} - {$ex->getMessage()}");
            sleep(2);
            $this->trySend($product, $try + 1);
        }
    }
}
