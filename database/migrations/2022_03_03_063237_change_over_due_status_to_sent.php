<?php

use Crater\Models\Invoice;
use Illuminate\Database\Migrations\Migration;

class ChangeOverDueStatusToSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $overdueInvoices = Invoice::where('status', 'OVERDUE')->get();

        if ($overdueInvoices) {
            $overdueInvoices->map(function ($overdueInvoice) {
                $overdueInvoice->status = Invoice::STATUS_SENT;
                $overdueInvoice->overdue = true;
                $overdueInvoice->save();
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
