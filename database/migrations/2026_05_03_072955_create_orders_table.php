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
            $table->integer('id', true);
            $table->integer('combined_order_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('guest_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->longText('shipping_address')->nullable();
            $table->string('delivery_status', 20)->nullable()->default('pending');
            $table->string('payment_type', 20)->nullable();
            $table->string('payment_status', 20)->nullable()->default('unpaid');
            $table->longText('payment_details')->nullable();
            $table->double('grand_total')->nullable();
            $table->double('coupon_discount')->default(0);
            $table->mediumText('code')->nullable();
            $table->string('tracking_code', 255)->nullable();
            $table->integer('date');
            $table->integer('viewed')->default(0);
            $table->integer('delivery_viewed')->default(1);
            $table->integer('payment_status_viewed')->nullable()->default(1);
            $table->integer('commission_calculated')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
