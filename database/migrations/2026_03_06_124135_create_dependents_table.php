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
        Schema::create('dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('no_kp', 20);
            $table->enum('pasangan', ['ya', 'tidak']);
            $table->enum('pertalian', [
                'suami',
                'isteri',
                'anak',
                'bapa kandung',
                'ibu kandung',
                'bapa mertua',
                'ibu mertua',
            ]);
            $table->string('no_tel', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependents');
    }
};
