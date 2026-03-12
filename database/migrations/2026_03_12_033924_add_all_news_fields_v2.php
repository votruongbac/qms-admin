<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('news', function (Blueprint $table) {
        if (!Schema::hasColumn('news', 'slug')) {
            $table->string('slug')->unique()->after('title');
        }
        if (!Schema::hasColumn('news', 'summary')) {
            $table->text('summary')->nullable()->after('slug');
        }
        if (!Schema::hasColumn('news', 'thumbnail')) {
            $table->string('thumbnail')->nullable()->after('content');
        }
        if (!Schema::hasColumn('news', 'category')) {
            $table->string('category')->default('Tin tức')->after('thumbnail');
        }
        if (!Schema::hasColumn('news', 'is_published')) {
            $table->boolean('is_published')->default(true)->after('category');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            //
        });

    }
};
