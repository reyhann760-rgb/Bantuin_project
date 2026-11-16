<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable();

            $table->dropForeign(['customer_id']);
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['service_offer_id']);

            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('provider_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('service_request_id')->references('id')->on('service_requests')->nullOnDelete();
            $table->foreign('service_offer_id')->references('id')->on('service_offers')->nullOnDelete();
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->foreign('provider_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['sender_id']);
            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['sender_id']);
            $table->foreign('conversation_id')->references('id')->on('conversations');
            $table->foreign('sender_id')->references('id')->on('users');
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->foreign('provider_id')->references('id')->on('users');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('users');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['service_offer_id']);
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('provider_id')->references('id')->on('users');
            $table->foreign('service_request_id')->references('id')->on('service_requests');
            $table->foreign('service_offer_id')->references('id')->on('service_offers');
            $table->dropColumn('completed_at');
        });
    }
};