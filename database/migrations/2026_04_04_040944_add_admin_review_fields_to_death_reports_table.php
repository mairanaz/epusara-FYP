<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('death_reports', function (Blueprint $table) {
            $table->string('verification_category')->nullable()->after('status');
            // ahli_khairat / tanggungan / bukan_ahli / warga_asing

            $table->foreignId('verified_by')->nullable()->after('verification_category')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('verified_at')->nullable()->after('verified_by');

            $table->string('burial_lot_no')->nullable()->after('verified_at');
            $table->date('burial_date')->nullable()->after('burial_lot_no');

            $table->text('admin_notes')->nullable()->after('burial_date');
        });
    }

    public function down(): void
    {
        Schema::table('death_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'verification_category',
                'verified_at',
                'burial_lot_no',
                'burial_date',
                'admin_notes',
            ]);
        });
    }
};