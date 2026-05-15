<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->date('billing_month')->nullable()->after('paid_month');
            $table->date('cycle_start')->nullable()->after('billing_month');
            $table->date('cycle_end')->nullable()->after('cycle_start');

            $table->index('billing_month');
            $table->index(['cycle_start', 'cycle_end']);
        });
    }

    public function down(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->dropIndex(['billing_month']);
            $table->dropIndex(['cycle_start', 'cycle_end']);

            $table->dropColumn([
                'billing_month',
                'cycle_start',
                'cycle_end',
            ]);
        });
    }
};