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
        Schema::create('compilation_list_option', function (Blueprint $table) {
            $table->unsignedBigInteger('compilation_id');
            $table->unsignedBigInteger('list_option_id');

            $table->foreign('compilation_id')
                ->references('id')
                ->on('compilations');
            $table->foreign('list_option_id')
                ->references('id')
                ->on('list_options');

            $table->primary(['compilation_id', 'list_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compilation_list_option');
    }
};
