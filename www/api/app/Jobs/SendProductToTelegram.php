<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProductToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ProductService $productService;

    public function __construct(protected int $productId)
    {
        $this->productService = app(ProductService::class);
    }

    public function handle(): void
    {
        $product = Product::findOrFail($this->productId);
        $this->productService->sendProductToTelegram($product);
    }
}
