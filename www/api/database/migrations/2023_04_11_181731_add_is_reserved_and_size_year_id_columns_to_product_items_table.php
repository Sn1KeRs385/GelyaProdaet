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
        Schema::table('product_items', function (Blueprint $table) {
            $table->unsignedBigInteger('size_year_id')
                ->nullable()
                ->default(null)
                ->after('size_id');
            $table->boolean('is_reserved')
                ->default(false)
                ->after('is_for_sale');

            $table->unsignedBigInteger('size_id')
                ->nullable()
                ->default(null)
                ->change();

            $table->foreign('size_year_id')
                ->references('id')
                ->on('list_options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            //
        });
    }
};
