<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\Setting;

class UpdateCraterVersion500 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '5.0.0');
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
