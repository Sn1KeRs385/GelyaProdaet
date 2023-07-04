<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ozon_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('size_id')
                ->nullable()
                ->default(null);
            $table->unsignedBigInteger('size_year_id')
                ->nullable()
                ->default(null);
            $table->unsignedInteger('count');
            $table->unsignedBigInteger('external_id')
                ->nullable()
                ->default(null);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->foreign('color_id')
                ->references('id')
                ->on('list_options');

            $table->foreign('size_id')
                ->references('id')
                ->on('list_options');

            $table->foreign('size_year_id')
                ->references('id')
                ->on('list_options');
        });

        \App\Models\OzonImportTaskResult::truncate();

        Schema::table('ozon_import_task_results', function (Blueprint $table) {
            $table->renameColumn('product_item_id', 'ozon_product_id');
            $table->renameColumn('product_id', 'external_product_id');
        });

        Schema::table('ozon_import_task_results', function (Blueprint $table) {
            $table->dropForeign('ozon_import_task_results_product_item_id_foreign');
            $table->foreign('ozon_product_id')
                ->references('id')
                ->on('ozon_products');
        });

        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('ozon_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\OzonImportTaskResult::truncate();

        Schema::table('ozon_import_task_results', function (Blueprint $table) {
            $table->renameColumn('ozon_product_id', 'product_item_id');
            $table->renameColumn('external_product_id', 'product_id');
        });

        Schema::table('ozon_import_task_results', function (Blueprint $table) {
            $table->dropForeign('ozon_import_task_results_ozon_product_id_foreign');
            $table->foreign('product_item_id')
                ->references('id')
                ->on('product_items');
        });

        Schema::table('product_items', function (Blueprint $table) {
            $table->unsignedBigInteger('ozon_product_id')
                ->nullable()
                ->default(null);
        });

        Schema::dropIfExists('ozon_products');
    }
};
