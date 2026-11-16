<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'id']);
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });
    }
};