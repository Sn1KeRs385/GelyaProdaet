<?php

namespace App\Jobs;

use App\Exceptions\HasUnreadyFileOnModelException;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;

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
        $hanNotUploadFiles = $product->files()
            ->whereNotIn('status', [FileStatus::FINISHED, FileStatus::DELETED, FileStatus::ERROR])
            ->exists();
        if ($hanNotUploadFiles) {
            SendProductToTelegram::dispatch($this->productId)->onQueue('tg_public_messages')->delay(10);
            throw new HasUnreadyFileOnModelException();
        }
        $this->productService->sendProductToTelegram($product);
    }
}
