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
        Schema::table('list_options', function (Blueprint $table) {
            $table->index(['group_slug', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_options', function (Blueprint $table) {
            $table->dropIndex(['group_slug', 'id']);
        });
    }
};
