<?php

namespace App\Observers;

use App\Jobs\SendProductToTelegram;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        SendProductToTelegram::dispatch($product)->onQueue('tg_public_messages')->afterCommit();
    }

    public function updated(Product $product): void
    {
        SendProductToTelegram::dispatch($product)->onQueue('tg_public_messages')->afterCommit();
    }
}
