<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->default(null);
            $table->string('url');
            $table->nullableMorphs('owner');
            $table->boolean('is_enabled')
                ->default(true);
            $table->timestamps();

            $table->unique(['url']);
        });

        Schema::table('site_pages', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('site_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_pages');
    }
};
