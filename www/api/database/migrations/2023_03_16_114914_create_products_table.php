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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')
                ->nullable();
            $table->unsignedBigInteger('country_id')
                ->nullable();
            $table->timestamps();

            $table->foreign('brand_id')
                ->on('list_options')
                ->references('id');

            $table->foreign('country_id')
                ->on('list_options')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
