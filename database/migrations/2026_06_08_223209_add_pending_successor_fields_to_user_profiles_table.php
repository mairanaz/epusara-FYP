<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingSuccessorFieldsToUserProfilesTable extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'replacement_dependent_id')) {
                $table->unsignedBigInteger('replacement_dependent_id')->nullable()->after('replacement_reason');
            }

            if (!Schema::hasColumn('user_profiles', 'replacement_status')) {
                $table->string('replacement_status')->nullable()->after('replacement_dependent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('user_profiles', 'replacement_dependent_id')) {
                $table->dropColumn('replacement_dependent_id');
            }

            if (Schema::hasColumn('user_profiles', 'replacement_status')) {
                $table->dropColumn('replacement_status');
            }
        });
    }
}