<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionFieldsToDependentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            if (!Schema::hasColumn('dependents', 'promoted_user_id')) {
                $table->unsignedBigInteger('promoted_user_id')->nullable()->after('tarikh_kematian');
            }

            if (!Schema::hasColumn('dependents', 'promoted_at')) {
                $table->timestamp('promoted_at')->nullable()->after('promoted_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            if (Schema::hasColumn('dependents', 'promoted_user_id')) {
                $table->dropColumn('promoted_user_id');
            }

            if (Schema::hasColumn('dependents', 'promoted_at')) {
                $table->dropColumn('promoted_at');
            }
        });
    }
}