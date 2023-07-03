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
        Schema::create('ozon_import_task_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_task_id');
            $table->unsignedBigInteger('product_item_id');
            $table->string('offer_id');
            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->default(null);
            $table->string('status')
                ->nullable()
                ->default(null);
            $table->jsonb('errors')
                ->nullable()
                ->default(null);
            $table->timestamps();

            $table->foreign('product_item_id')
                ->references('id')
                ->on('product_items');
            $table->foreign('import_task_id')
                ->references('id')
                ->on('ozon_import_tasks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_import_task_results');
    }
};
