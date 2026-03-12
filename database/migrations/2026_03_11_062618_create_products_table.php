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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cột bị báo thiếu
            $table->decimal('price', 15, 2)->nullable();
            $table->string('category')->nullable();
            $table->string('image_main')->nullable();
            $table->json('images_sub')->nullable(); // Lưu mảng nhiều ảnh
            $table->json('specs')->nullable();      // Lưu thông số kỹ thuật
            $table->text('description')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
