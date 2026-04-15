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
        Schema::table('death_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('burial_plot_id')->nullable()->after('status');
            $table->string('burial_zone')->nullable()->after('burial_plot_id');
            $table->string('burial_plot_code')->nullable()->after('burial_zone');
            $table->date('tarikh_kebumi')->nullable()->after('burial_plot_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('death_reports', function (Blueprint $table) {
             $table->dropColumn([
                'burial_plot_id',
                'burial_zone',
                'burial_plot_code',
                'tarikh_kebumi',
            ]);
        });
    }
};
