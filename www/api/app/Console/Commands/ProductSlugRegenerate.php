<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ProductSlugRegenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:slug-regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate product slug';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $product->slug = $product->id . '-' . Str::slug($product->title);
                $product->saveQuietly();
            }
        });
    }
}
