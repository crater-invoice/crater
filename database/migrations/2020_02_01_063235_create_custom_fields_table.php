<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('label');
            $table->string('model_type');
            $table->string('type');
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable();
            $table->boolean('boolean_answer')->nullable();
            $table->date('date_answer')->nullable();
            $table->time('time_answer')->nullable();
            $table->text('string_answer')->nullable();
            $table->unsignedBigInteger('number_answer')->nullable();
            $table->dateTime('date_time_answer')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedBigInteger('order')->default(1);
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
        Schema::dropIfExists('custom_fields');
    }
}
