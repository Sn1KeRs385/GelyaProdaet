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
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('color_id')
                ->nullable()
                ->default(null);
            $table->unsignedBigInteger('price');
            $table->boolean('is_sold')
                ->default(false);
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id');

            $table->foreign('size_id')
                ->on('list_options')
                ->references('id');

            $table->foreign('color_id')
                ->on('list_options')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
