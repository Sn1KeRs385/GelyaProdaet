<?php

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
            $table->timestamp('sold_at')
                ->nullable()
                ->default(null);

            $table->index(['sold_at']);
        });

        DB::table('product_items')
            ->where('is_sold', true)
            ->update([
                'sold_at' => DB::raw('updated_at'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('sold_at');
        });
    }
};
