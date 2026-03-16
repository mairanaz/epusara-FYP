<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->year('membership_year')->nullable()->after('amount');
            $table->unsignedTinyInteger('paid_month')->nullable()->after('membership_year');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['membership_year', 'paid_month']);
        });
    }
};