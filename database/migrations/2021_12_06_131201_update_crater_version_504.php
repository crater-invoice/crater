<?php

use Crater\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class UpdateCraterVersion504 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '5.0.4');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
