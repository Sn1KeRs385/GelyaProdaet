<?php

namespace App\Console\Commands;

use App\Models\OzonProduct;
use App\Models\ProductItem;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;

class OzonProductsCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:products-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create ozon products from products items';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $productItems = ProductItem::query()
            ->select([
                'product_items.product_id',
                'product_items.size_id',
                'product_items.color_id',
                'product_items.size_year_id',
                'product_items.count',
                'op.id',
            ])
            ->leftJoin('ozon_products as op', function (JoinClause $query) {
                $query->on('op.product_id', 'is not distinct from', 'product_items.product_id')
                    ->on('op.size_id', 'is not distinct from', 'product_items.size_id')
                    ->on('op.color_id', 'is not distinct from', 'product_items.color_id')
                    ->on('op.size_year_id', 'is not distinct from', 'product_items.size_year_id')
                    ->on('op.count', 'is not distinct from', 'product_items.count');
            })
            ->groupBy(
                [
                    'product_items.product_id',
                    'product_items.size_id',
                    'product_items.color_id',
                    'product_items.size_year_id',
                    'product_items.count',
                    'op.id',
                ]
            )
            ->orderBy('product_items.product_id')
            ->whereNull('op.id')
            ->whereNotNull('product_items.color_id')
            ->get();

        foreach ($productItems as $productItem) {
            OzonProduct::updateOrCreate([
                'product_id' => $productItem->product_id,
                'size_id' => $productItem->size_id,
                'color_id' => $productItem->color_id,
                'size_year_id' => $productItem->size_year_id,
                'count' => $productItem->count,
            ]);
        }
    }
}
