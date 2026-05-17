<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payku_transactions')) {
            Schema::create('payku_transactions', function (Blueprint $table) {
                $table->string('id')->unique();
                $table->string('status')->nullable();
                $table->string('order')->nullable()->unique();
                $table->string('email')->nullable();
                $table->string('subject')->nullable();
                $table->text('url')->nullable();
                $table->unsignedInteger('amount')->nullable();
                $table->dateTime('notified_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payku_payments')) {
            Schema::create('payku_payments', function (Blueprint $table) {
                $table->string('transaction_id')->unique();
                $table->date('start');
                $table->date('end');
                $table->string('media');
                $table->string('transaction_key', 255)->nullable();
                $table->string('payment_key', 255)->nullable();
                $table->string('verification_key');
                $table->string('authorization_code');
                $table->unsignedInteger('last_4_digits')->nullable();
                $table->string('installments')->nullable();
                $table->string('card_type')->nullable();
                $table->string('additional_parameters')->nullable();
                $table->string('currency');
                $table->timestamps();
                $table->foreign('transaction_id')->references('id')->on('payku_transactions');
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('payku_payments');
        Schema::dropIfExists('payku_transactions');
    }
};
