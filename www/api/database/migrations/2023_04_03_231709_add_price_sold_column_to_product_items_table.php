<?php

use App\Models\ProductItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->unsignedBigInteger('price_sell')
                ->nullable()
                ->default(null)
                ->after('price');
        });

        ProductItem::withoutEvents(function () {
            ProductItem::query()
                ->where('is_sold', true)
                ->update(['price_sell' => DB::raw('price')]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('price_sell');
        });
    }
};
