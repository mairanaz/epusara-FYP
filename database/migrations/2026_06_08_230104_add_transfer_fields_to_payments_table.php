<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferFieldsToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'original_user_id')) {
                $table->unsignedBigInteger('original_user_id')
                    ->nullable()
                    ->after('user_id');
            }

            if (!Schema::hasColumn('payments', 'transferred_from_user_id')) {
                $table->unsignedBigInteger('transferred_from_user_id')
                    ->nullable()
                    ->after('original_user_id');
            }

            if (!Schema::hasColumn('payments', 'transferred_to_user_id')) {
                $table->unsignedBigInteger('transferred_to_user_id')
                    ->nullable()
                    ->after('transferred_from_user_id');
            }

            if (!Schema::hasColumn('payments', 'transferred_at')) {
                $table->timestamp('transferred_at')
                    ->nullable()
                    ->after('transferred_to_user_id');
            }

            if (!Schema::hasColumn('payments', 'transfer_reason')) {
                $table->string('transfer_reason')
                    ->nullable()
                    ->after('transferred_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'transfer_reason')) {
                $table->dropColumn('transfer_reason');
            }

            if (Schema::hasColumn('payments', 'transferred_at')) {
                $table->dropColumn('transferred_at');
            }

            if (Schema::hasColumn('payments', 'transferred_to_user_id')) {
                $table->dropColumn('transferred_to_user_id');
            }

            if (Schema::hasColumn('payments', 'transferred_from_user_id')) {
                $table->dropColumn('transferred_from_user_id');
            }

            if (Schema::hasColumn('payments', 'original_user_id')) {
                $table->dropColumn('original_user_id');
            }
        });
    }
}