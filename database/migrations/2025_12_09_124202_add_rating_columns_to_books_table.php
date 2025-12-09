<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('reviews_avg_rating', 3, 2)->nullable()->default(null)->after('stock');
            $table->integer('reviews_count')->default(0)->after('reviews_avg_rating');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['reviews_avg_rating', 'reviews_count']);
        });
    }
};