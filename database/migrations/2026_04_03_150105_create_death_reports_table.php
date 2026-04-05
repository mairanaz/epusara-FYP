<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('death_reports', function (Blueprint $table) {
            $table->id();

            $table->string('deceased_type'); // member / dependent

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dependent_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nama_si_mati');
            $table->string('no_kp_si_mati');
            $table->string('jantina')->nullable();
            $table->text('alamat_terakhir')->nullable();
            $table->date('tarikh_meninggal');
            $table->integer('umur')->nullable();
            $table->string('no_permit_kebumi')->nullable();

            $table->string('nama_pelapor');
            $table->string('no_kp_pelapor')->nullable();
            $table->string('no_tel_pelapor');
            $table->string('pertalian_pelapor')->nullable();

            $table->string('sijil_mati_path')->nullable();
            $table->string('permit_kebumi_path')->nullable();
            $table->string('dokumen_sokongan_path')->nullable();

            $table->string('status')->default('menunggu_semakan');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('death_reports');
    }
};