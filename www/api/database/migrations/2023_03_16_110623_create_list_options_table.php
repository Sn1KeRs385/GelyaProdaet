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
        Schema::create('list_options', function (Blueprint $table) {
            $table->id();
            $table->enum('group_slug', Arr::pluck(\App\Enums\OptionGroupSlug::cases(), 'value'));
            $table->string('title');
            $table->integer('weight')
                ->default(0);
            $table->timestamps();

            $table->index(['group_slug', 'title']);
            $table->index(['group_slug', 'weight', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_options');
    }
};
