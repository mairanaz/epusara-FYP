<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type')->default('utama')->after('role');
            $table->unsignedBigInteger('linked_profile_id')->nullable()->after('account_type');
            $table->unsignedBigInteger('linked_dependent_id')->nullable()->after('linked_profile_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'account_type',
                'linked_profile_id',
                'linked_dependent_id',
            ]);
        });
    }
};