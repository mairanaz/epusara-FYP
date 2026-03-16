<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('payment_plan'); // bulanan / tahunan

            $table->string('payment_type'); // pendaftaran / bulanan / tahunan / upgrade_tahunan
            $table->decimal('amount', 10, 2);

            $table->string('payment_period')->nullable(); // contoh: 2026-03 atau 2026
            $table->string('status')->default('pending'); // pending / paid / failed / cancelled

            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // manual / online
            $table->string('reference_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};