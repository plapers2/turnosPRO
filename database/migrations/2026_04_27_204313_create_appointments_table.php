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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->dateTime("start_time");
            $table->dateTime("end_time");
            $table->text('cancellation_reason')->nullable();
            $table->time('payment_expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('cancel_token', 45)->nullable();
            $table->dateTime("cancel_token_expires_at")->nullable();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('company_id')->constrained();
            $table->enum('status', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->uuid('booking_group')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
