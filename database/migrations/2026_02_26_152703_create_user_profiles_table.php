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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->string('no_kp')->nullable();
            $table->string('email')->nullable();
            $table->date('tarikh')->nullable();
            $table->string('alamat_rumah')->nullable();
            $table->string('no_tel_rumah')->nullable();
            $table->string('no_tel')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('alamat_kerja')->nullable();
            $table->timestamps();
            $table->unique('user_id'); // satu user satu profile
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
