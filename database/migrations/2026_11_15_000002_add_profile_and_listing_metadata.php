<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable();
        });

        DB::table('categories')->insertOrIgnore([
            'name' => 'Belum dikategorikan',
            'slug' => 'uncategorized',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uncategorizedId = DB::table('categories')
            ->where('slug', 'uncategorized')
            ->value('id');

        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
        });

        DB::table('service_requests')
            ->whereNull('category_id')
            ->update(['category_id' => $uncategorizedId]);

        DB::table('service_offers')
            ->whereNull('category_id')
            ->update(['category_id' => $uncategorizedId]);
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'image']);
        });

        Schema::table('service_offers', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'image']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};