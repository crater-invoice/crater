<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\Setting;

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
