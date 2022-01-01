<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseColumnsIntoInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('exchange_rate', 19, 6)->nullable();
            $table->unsignedBigInteger('base_discount_val')->nullable();
            $table->unsignedBigInteger('base_sub_total')->nullable();
            $table->unsignedBigInteger('base_total')->nullable();
            $table->unsignedBigInteger('base_tax')->nullable();
            $table->unsignedBigInteger('base_due_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'base_discount_val',
                'exchange_rate',
                'base_sub_total',
                'base_total',
                'base_tax',
                'base_due_amount'
            ]);
        });
    }
}
