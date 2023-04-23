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
        Schema::table('tg_messages', function (Blueprint $table) {
            $table->renameColumn('is_forward_error', 'is_not_found_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tg_messages', function (Blueprint $table) {
            $table->renameColumn('is_not_found_error', 'is_forward_error');
        });
    }
};
