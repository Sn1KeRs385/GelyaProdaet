<?php

namespace App\Observers;

use App\Jobs\SendProductToTelegram;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        SendProductToTelegram::dispatch($product->id)->onQueue('tg_public_messages')->delay(now()->addSeconds(10));
    }

    public function updated(Product $product): void
    {
        SendProductToTelegram::dispatch($product->id)->onQueue('tg_public_messages')->delay(now()->addSeconds(10));
    }
}
