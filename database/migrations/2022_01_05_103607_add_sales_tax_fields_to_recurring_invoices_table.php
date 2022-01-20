<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesTaxFieldsToRecurringInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->string('sales_tax_type')->nullable();
            $table->string('sales_tax_address_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'sales_tax_type',
                'sales_tax_address_type',
            ]);
        });
    }
}
