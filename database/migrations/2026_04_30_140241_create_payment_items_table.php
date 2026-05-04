<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->string('payment_type');
            // registration / monthly / yearly

            $table->decimal('amount', 10, 2);

            $table->year('membership_year')->nullable();

            $table->unsignedTinyInteger('paid_month')->nullable();

            $table->string('payment_period')->nullable();
            // registration: null
            // monthly: 2026-04
            // yearly: 2026

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['payment_type', 'payment_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};