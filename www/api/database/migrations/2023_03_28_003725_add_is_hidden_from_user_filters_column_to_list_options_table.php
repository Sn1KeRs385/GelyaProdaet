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
            $table->boolean('is_hidden_from_user_filters')
                ->default(false)
                ->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_options', function (Blueprint $table) {
            $table->dropColumn('is_hidden_from_user_filters');
        });
    }
};
