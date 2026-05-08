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
        Schema::create('grave_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('death_report_id')
                ->constrained('death_reports')
                ->cascadeOnDelete();

            $table->foreignId('burial_plot_id')
                ->nullable()
                ->constrained('burial_plots')
                ->nullOnDelete();

            // Kategori: dewasa / kanak-kanak
            $table->string('category');

            // Jenis tempahan, contoh: marble_full, tiles_nisan
            $table->string('order_type');

            // Nama tempahan, contoh: Set Kepungan Marble/Marmar Penuh
            $table->string('order_label');

            // Jumlah bayaran
            $table->decimal('amount', 10, 2)->default(0);

            // Perakuan user
            $table->boolean('declaration')->default(false);

            // Status permohonan: pending, approved, rejected, completed
            $table->string('status')->default('pending');

            // Bahagian admin
            $table->text('admin_note')->nullable();
            $table->string('receipt_no')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grave_orders');
    }
};