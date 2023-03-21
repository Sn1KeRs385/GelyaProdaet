<?php

namespace App\Observers;

use App\Jobs\SendProductToTelegram;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        SendProductToTelegram::dispatchAfterResponse($product)->onQueue('tg_public_messages');
    }

    public function updated(Product $product): void
    {
        SendProductToTelegram::dispatchAfterResponse($product)->onQueue('tg_public_messages');
    }
}
