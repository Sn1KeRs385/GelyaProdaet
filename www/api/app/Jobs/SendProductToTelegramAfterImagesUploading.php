<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;
use Sn1KeRs385\FileUploader\App\Models\File;

class SendProductToTelegramAfterImagesUploading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ProductService $productService;

    public function __construct(protected File $file)
    {
        $this->file->refresh();
        $this->productService = app(ProductService::class);
    }

    public function handle(): void
    {
        if ($this->file->owner_type !== (new Product())->getMorphClass()) {
            echo "File not product";
            return;
        }
        /** @var Product $product */
        $product = $this->file->owner()->first();
        $hanNotUploadFiles = $product->files()
            ->whereNotIn('status', [FileStatus::FINISHED, FileStatus::DELETED, FileStatus::ERROR])
            ->exists();
        if ($hanNotUploadFiles) {
            echo 'HasUnreadyFile';
            return;
        }

        SendProductToTelegram::dispatch($product->id)->onQueue('tg_public_messages');
    }
}
