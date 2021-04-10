<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitNameToPdf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('unit_name')->nullable()->after('quantity');
        });
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->string('unit_name')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('unit_name');
        });
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->dropColumn('unit_name');
        });
    }
}
