<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('status_kehidupan')->default('aktif')->after('catatan_permohonan');
            $table->date('tarikh_kematian')->nullable()->after('status_kehidupan');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['status_kehidupan', 'tarikh_kematian']);
        });
    }
};