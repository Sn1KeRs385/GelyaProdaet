<?php

namespace App\Console\Commands;

use App\Bots\Telegram\TelegramBot;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Console\Command;

class ProductTelegramUpdateAll extends Command
{
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
        /** @var ProductService $productService */
        $productService = app(ProductService::class);

        Product::query()
            ->chunk(500, function ($products) use ($productService) {
                /** @var Product[] $products */
                foreach ($products as $product) {
                    try {
                        $productService->sendProductToTelegram($product);
                    } catch (\Throwable $ex) {
                        dump("Product {$product->id} - {$ex->getMessage()}");
                    }
                    sleep(2);
                }
            });
    }
}
