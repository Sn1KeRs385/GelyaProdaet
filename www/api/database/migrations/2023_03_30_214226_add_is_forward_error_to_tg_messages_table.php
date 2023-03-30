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
            $table->boolean('is_forward_error')
                ->after('file_ids')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tg_messages', function (Blueprint $table) {
            $table->dropColumn('is_forward_error');
        });
    }
};
