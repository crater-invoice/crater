<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\Setting;

class UpdateCraterVersion410 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::setSetting('version', '4.1.0');
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
