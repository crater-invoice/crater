<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\Setting;

class UpdateCraterVersion505 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '5.0.5');
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
