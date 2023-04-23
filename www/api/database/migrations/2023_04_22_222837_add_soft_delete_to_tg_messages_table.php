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
            $table->jsonb('extra_message_ids')
                ->default('[]');
            $table->softDeletes();
            $table->boolean('is_messages_deleted')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tg_messages', function (Blueprint $table) {
            $table->dropColumn('extra_message_ids');
            $table->dropSoftDeletes();
            $table->dropColumn('is_messages_deleted');
        });
    }
};
