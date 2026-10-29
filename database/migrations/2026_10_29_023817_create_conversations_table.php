<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users');

            $table->foreignId('provider_id')
                ->constrained('users');

            $table->foreignId('service_request_id')
                ->nullable()
                ->constrained('service_requests');

            $table->foreignId('service_offer_id')
                ->nullable()
                ->constrained('service_offers');

            $table->string('status', 30)->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
