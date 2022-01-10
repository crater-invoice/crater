<?php

use Crater\Models\TaxType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToTaxTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_types', function (Blueprint $table) {
            $table->enum('type', ['GENERAL', 'MODULE'])->default(TaxType::TYPE_GENERAL);
        });

        $taxTypes = TaxType::all();

        if ($taxTypes) {
            foreach ($taxTypes as $taxType) {
                $taxType->type = TaxType::TYPE_GENERAL;
                $taxType->save();
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
        Schema::table('tax_types', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
