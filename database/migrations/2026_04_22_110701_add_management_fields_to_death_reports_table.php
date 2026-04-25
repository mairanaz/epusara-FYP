<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('death_reports', function (Blueprint $table) {
            $table->string('sebab_kematian')->nullable()->after('tarikh_meninggal');

            $table->string('lokasi_mandi_jenazah')->nullable()->after('sebab_kematian');
            $table->string('pengurusan_jenazah_oleh')->nullable()->after('lokasi_mandi_jenazah');

            $table->string('lokasi_pengkebumian')->nullable()->after('pengurusan_jenazah_oleh');
            $table->string('nama_tanah_perkuburan')->nullable()->after('lokasi_pengkebumian');
            $table->text('alamat_tanah_perkuburan')->nullable()->after('nama_tanah_perkuburan');
            $table->string('negeri_tanah_perkuburan')->nullable()->after('alamat_tanah_perkuburan');

            $table->text('catatan_pengurusan')->nullable()->after('negeri_tanah_perkuburan');
        });
    }

    public function down(): void
    {
        Schema::table('death_reports', function (Blueprint $table) {
            $table->dropColumn([
                'sebab_kematian',
                'lokasi_mandi_jenazah',
                'pengurusan_jenazah_oleh',
                'lokasi_pengkebumian',
                'nama_tanah_perkuburan',
                'alamat_tanah_perkuburan',
                'negeri_tanah_perkuburan',
                'catatan_pengurusan',
            ]);
        });
    }
};