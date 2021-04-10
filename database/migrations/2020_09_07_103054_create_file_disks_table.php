<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileDisksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_disks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('REMOTE');
            $table->string('driver');
            $table->boolean('set_as_default')->default(false);
            $table->json('credentials');
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
        Schema::dropIfExists('file_disks');
    }
}
