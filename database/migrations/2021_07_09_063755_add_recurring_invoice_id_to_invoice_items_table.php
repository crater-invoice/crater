<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurringInvoiceIdToInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable()->change();
            $table->unsignedBigInteger('recurring_invoice_id')->nullable();
            $table->foreign('recurring_invoice_id')->references('id')->on('recurring_invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['recurring_invoice_id']);
            }
            $table->dropColumn('recurring_invoice_id');
        });
    }
}
