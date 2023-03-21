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
            $table->string('title');
            $table->text('description')
                ->nullable()
                ->after('title');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('gender_id')
                ->after('type_id');
            $table->unsignedBigInteger('brand_id')
                ->nullable();
            $table->unsignedBigInteger('country_id')
                ->nullable();
            $table->timestamps();

            $table->foreign('type_id')
                ->on('list_options')
                ->references('id');

            $table->foreign('gender_id')
                ->on('list_options')
                ->references('id');

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
