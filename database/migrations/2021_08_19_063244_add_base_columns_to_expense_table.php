<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseColumnsToExpenseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('exchange_rate', 19, 6)->nullable();
            $table->unsignedBigInteger('base_amount')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'exchange_rate',
                'base_amount',
                'currency_id',
            ]);
        });
    }
}
