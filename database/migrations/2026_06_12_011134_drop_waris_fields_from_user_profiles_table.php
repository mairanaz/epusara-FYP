<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('user_profiles', 'nama_waris')) {
                $table->dropColumn('nama_waris');
            }

            if (Schema::hasColumn('user_profiles', 'hubungan_waris')) {
                $table->dropColumn('hubungan_waris');
            }

            if (Schema::hasColumn('user_profiles', 'no_tel_waris')) {
                $table->dropColumn('no_tel_waris');
            }

            if (Schema::hasColumn('user_profiles', 'alamat_waris')) {
                $table->dropColumn('alamat_waris');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'nama_waris')) {
                $table->string('nama_waris')->nullable()->after('tempoh_menetap');
            }

            if (!Schema::hasColumn('user_profiles', 'hubungan_waris')) {
                $table->string('hubungan_waris')->nullable()->after('nama_waris');
            }

            if (!Schema::hasColumn('user_profiles', 'no_tel_waris')) {
                $table->string('no_tel_waris')->nullable()->after('hubungan_waris');
            }

            if (!Schema::hasColumn('user_profiles', 'alamat_waris')) {
                $table->string('alamat_waris')->nullable()->after('no_tel_waris');
            }
        });
    }
};