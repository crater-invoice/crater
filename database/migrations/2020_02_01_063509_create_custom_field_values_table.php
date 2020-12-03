<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_field_valuable_type');
            $table->unsignedInteger('custom_field_valuable_id');
            $table->string('type');
            $table->boolean('boolean_answer')->nullable();
            $table->date('date_answer')->nullable();
            $table->time('time_answer')->nullable();
            $table->text('string_answer')->nullable();
            $table->unsignedBigInteger('number_answer')->nullable();
            $table->dateTime('date_time_answer')->nullable();
            $table->unsignedBigInteger('custom_field_id');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('answers');
    }
}
