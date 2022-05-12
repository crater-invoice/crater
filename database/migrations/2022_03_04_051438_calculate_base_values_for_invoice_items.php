<?php

use Crater\Models\InvoiceItem;
use Crater\Models\Tax;
use Illuminate\Database\Migrations\Migration;

class CalculateBaseValuesForInvoiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $taxes = Tax::whereRelation('invoiceItem', 'base_amount', null)->get();

        if ($taxes) {
            $taxes->map(function ($tax) {
                $invoiceItem = InvoiceItem::find($tax->invoice_item_id);
                $exchange_rate = $invoiceItem->exchange_rate;
                $tax->exchange_rate = $exchange_rate;
                $tax->base_amount = $tax->amount * $exchange_rate;
                $tax->save();
            });
        }
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
