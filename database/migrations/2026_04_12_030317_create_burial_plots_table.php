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
        Schema::create('burial_plots', function (Blueprint $table) {
            $table->id();
            $table->string('zone', 1); // K, P, L
            $table->integer('row_number'); // contoh 1,2,3
            $table->integer('lot_number'); // contoh 1,2,3
            $table->string('plot_code')->unique(); // contoh K-B1-01
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->unsignedBigInteger('death_report_id')->nullable();
            $table->date('buried_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burial_plots');
    }
};
