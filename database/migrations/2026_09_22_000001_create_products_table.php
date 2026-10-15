<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Kode kategori (string, bukan enum) supaya kategori bisa ditambah dari panel admin
            $table->string('category', 50)->index();
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->enum('badge', ['best_seller', 'baru', 'pedas'])->nullable();
            $table->string('image_path')->nullable();
            $table->string('whatsapp_order_link')->nullable();
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('stock')->default(45);
            $table->unsignedInteger('min_stock')->default(10);
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
