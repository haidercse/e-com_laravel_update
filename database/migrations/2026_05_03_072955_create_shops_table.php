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
        Schema::create('shops', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('name', 200)->nullable();
            $table->string('logo', 255)->nullable();
            $table->longText('sliders')->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('facebook', 255)->nullable();
            $table->string('google', 255)->nullable();
            $table->string('twitter', 255)->nullable();
            $table->string('youtube', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('pick_up_point_id')->nullable();
            $table->double('shipping_cost')->default(0);
            $table->float('delivery_pickup_latitude')->nullable();
            $table->float('delivery_pickup_longitude')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
