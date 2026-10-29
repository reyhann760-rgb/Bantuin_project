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
        Schema::create('service_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provider_id')
                ->constrained('users');

            $table->string('title');
            $table->text('description');

            $table->decimal('price', 12, 2)->nullable();
            $table->string('price_type', 30)->nullable();

            $table->string('work_mode', 50)->nullable();
            $table->string('location')->nullable();
            $table->string('portfolio_url')->nullable();

            $table->string('status', 30)->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_offers');
    }
};
