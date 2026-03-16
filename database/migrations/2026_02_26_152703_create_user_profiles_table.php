<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Maklumat ahli
            $table->string('nama');
            $table->string('no_kp')->unique();
            $table->date('tarikh_lahir')->nullable();
            $table->string('agama')->default('Islam');
            $table->string('warganegara')->default('Malaysia');

            // Perhubungan
            $table->text('alamat_rumah');
            $table->string('no_tel_rumah')->nullable();
            $table->string('no_tel_bimbit');

            // Kelayakan kariah
            $table->boolean('tinggal_dalam_kariah')->default(false);
            $table->string('tempoh_menetap')->nullable();

            // Pekerjaan
            $table->string('pekerjaan')->nullable();
            $table->string('nama_majikan')->nullable();
            $table->text('alamat_kerja')->nullable();

            // Waris
            $table->string('nama_waris')->nullable();
            $table->string('hubungan_waris')->nullable();
            $table->string('no_tel_waris')->nullable();
            $table->text('alamat_waris')->nullable();

            // Permohonan
            $table->date('tarikh_permohonan')->nullable();
            $table->string('status_permohonan')->default('draft');
            $table->text('catatan_permohonan')->nullable();

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};