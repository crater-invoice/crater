<?php

use Crater\Models\Payment;
use Illuminate\Database\Migrations\Migration;

class CalculateBaseAmountOfPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $payments = Payment::where('exchange_rate', '<>', null)->get();

        if ($payments) {
            foreach ($payments as $payment) {
                $payment->base_amount = $payment->exchange_rate * $payment->amount;
                $payment->save();
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
        //
    }
}
