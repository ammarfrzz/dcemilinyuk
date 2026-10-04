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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('shipping_address');
            $table->text('order_notes')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name');
            $table->integer('unit_price')->default(0);
            $table->integer('items_count')->default(1);
            $table->integer('shipping_fee')->default(0);
            $table->integer('service_fee')->default(0);
            $table->integer('total_amount')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('Diproses');
            $table->timestamp('order_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
