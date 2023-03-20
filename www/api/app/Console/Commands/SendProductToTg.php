<?php

namespace App\Console\Commands;

use App\Enums\IdentifierType;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SendProductToTg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:send-tg {id}';

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
