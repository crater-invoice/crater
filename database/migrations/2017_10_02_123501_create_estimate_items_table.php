<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('discount_type');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('discount');
            $table->unsignedBigInteger('discount_val');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('tax');
            $table->unsignedBigInteger('total');
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->integer('estimate_id')->unsigned();
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimate_items');
    }
}
