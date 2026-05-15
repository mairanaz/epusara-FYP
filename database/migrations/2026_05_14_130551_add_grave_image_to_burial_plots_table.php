<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('burial_plots', function (Blueprint $table) {
            $table->string('grave_image')->nullable()->after('buried_at');
            $table->timestamp('grave_image_updated_at')->nullable()->after('grave_image');
        });
    }

    public function down(): void
    {
        Schema::table('burial_plots', function (Blueprint $table) {
            $table->dropColumn(['grave_image', 'grave_image_updated_at']);
        });
    }
};