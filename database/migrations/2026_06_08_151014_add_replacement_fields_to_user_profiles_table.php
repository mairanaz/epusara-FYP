<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReplacementFieldsToUserProfilesTable extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'replaced_by_user_id')) {
                $table->unsignedBigInteger('replaced_by_user_id')->nullable()->after('tarikh_kematian');
            }

            if (!Schema::hasColumn('user_profiles', 'replaced_at')) {
                $table->timestamp('replaced_at')->nullable()->after('replaced_by_user_id');
            }

            if (!Schema::hasColumn('user_profiles', 'replacement_reason')) {
                $table->string('replacement_reason')->nullable()->after('replaced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('user_profiles', 'replaced_by_user_id')) {
                $table->dropColumn('replaced_by_user_id');
            }

            if (Schema::hasColumn('user_profiles', 'replaced_at')) {
                $table->dropColumn('replaced_at');
            }

            if (Schema::hasColumn('user_profiles', 'replacement_reason')) {
                $table->dropColumn('replacement_reason');
            }
        });
    }
}