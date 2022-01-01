<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseColumnsIntoEstimateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->decimal('exchange_rate', 19, 6)->nullable();
            $table->unsignedBigInteger('base_discount_val')->nullable();
            $table->unsignedBigInteger('base_price')->nullable();
            $table->unsignedBigInteger('base_tax')->nullable();
            $table->unsignedBigInteger('base_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->dropColumn([
                'exchange_rate',
                'base_discount_val',
                'base_price',
                'base_tax',
                'base_total'
            ]);
        });
    }
}
