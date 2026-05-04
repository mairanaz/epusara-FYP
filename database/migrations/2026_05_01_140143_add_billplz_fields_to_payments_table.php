<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillplzFieldsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('billplz_bill_id')->nullable()->after('receipt_no');
            $table->text('billplz_url')->nullable()->after('billplz_bill_id');
            $table->boolean('billplz_paid')->default(false)->after('billplz_url');
            $table->string('billplz_state')->nullable()->after('billplz_paid');
            $table->json('billplz_data')->nullable()->after('billplz_state');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'billplz_bill_id',
                'billplz_url',
                'billplz_paid',
                'billplz_state',
                'billplz_data',
            ]);
        });
    }
}