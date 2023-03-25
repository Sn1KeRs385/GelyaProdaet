<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Console\Command;

class ProductTgSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:tg-send {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send product to tg';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!$this->argument('id')) {
            throw new \Exception('Id is required');
        }
        $product = Product::findOrFail($this->argument('id'));

        /** @var ProductService $productService */
        $productService = app(ProductService::class);

        $productService->sendProductToTelegram($product);
    }
}
