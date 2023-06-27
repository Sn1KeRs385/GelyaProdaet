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
        Schema::table('compilations', function (Blueprint $table) {
            $table->boolean('is_show_on_header')
                ->after('name')
                ->default(false);
            $table->boolean('is_show_on_footer')
                ->after('is_show_on_header')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compilations', function (Blueprint $table) {
            $table->dropColumn('is_show_on_header');
            $table->dropColumn('is_show_on_footer');
        });
    }
};
