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
        Schema::create('ozon_attribute_bindings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('list_option_id');
            $table->unsignedBigInteger('dictionary_value_id');
            $table->string('dictionary_value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('list_option_id')
                ->references('id')
                ->on('list_options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_attribute_bindings');
    }
};
