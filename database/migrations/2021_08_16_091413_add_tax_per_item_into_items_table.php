<?php

use Crater\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxPerItemIntoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('tax_per_item')->default(false);
        });

        $items = Item::with('taxes')->get();

        if ($items) {
            foreach ($items as $item) {
                if (! $item->taxes()->get()->isEmpty()) {
                    $item->tax_per_item = true;
                    $item->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('tax_per_item');
        });
    }
}
