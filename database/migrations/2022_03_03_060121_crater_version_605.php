<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\Setting;

class CraterVersion605 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '6.0.5');
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
