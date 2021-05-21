<?php

use Crater\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class UpdateCraterVersion403 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '4.0.3');
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
