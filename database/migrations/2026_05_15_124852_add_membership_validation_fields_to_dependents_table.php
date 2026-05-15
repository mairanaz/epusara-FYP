<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            if (!Schema::hasColumn('dependents', 'status_perkahwinan')) {
                $table->enum('status_perkahwinan', ['bujang', 'berkahwin', 'duda', 'janda'])
                    ->nullable()
                    ->after('pertalian');
            }

            if (!Schema::hasColumn('dependents', 'tinggal_bersama')) {
                $table->boolean('tinggal_bersama')
                    ->default(true)
                    ->after('status_perkahwinan');
            }

            if (!Schema::hasColumn('dependents', 'status_tanggungan')) {
                $table->enum('status_tanggungan', ['aktif', 'tidak_layak', 'meninggal'])
                    ->default('aktif')
                    ->after('tinggal_bersama');
            }

            if (!Schema::hasColumn('dependents', 'sebab_tidak_layak')) {
                $table->string('sebab_tidak_layak')->nullable()->after('status_tanggungan');
            }

            if (!Schema::hasColumn('dependents', 'tarikh_keluar_tanggungan')) {
                $table->date('tarikh_keluar_tanggungan')->nullable()->after('sebab_tidak_layak');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            $table->dropColumn([
                'status_perkahwinan',
                'tinggal_bersama',
                'status_tanggungan',
                'sebab_tidak_layak',
                'tarikh_keluar_tanggungan',
            ]);
        });
    }
};