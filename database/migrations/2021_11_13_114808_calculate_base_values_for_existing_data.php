<?php

use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Crater\Models\Item;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;

class CalculateBaseValuesForExistingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where('role', 'super admin')->first();

        if ($user) {
            $companyId = $user->companies()->first()->id;

            $currency_id = CompanySetting::getSetting('currency', $companyId);

            $items = Item::all();

            foreach ($items as $item) {
                $item->currency_id = $currency_id;
                $item->save();
            }

            $customers = Customer::all();

            foreach ($customers as $customer) {
                if ($customer->invoices()->exists()) {
                    $customer->invoices->map(function ($invoice) use ($currency_id, $customer) {
                        if ($customer->currency_id == $currency_id) {
                            $invoice->update([
                                'currency_id' => $currency_id,
                                'exchange_rate' => 1,
                                'base_discount_val' => $invoice->sub_total,
                                'base_sub_total' => $invoice->sub_total,
                                'base_total' => $invoice->total,
                                'base_tax' => $invoice->tax,
                                'base_due_amount' => $invoice->due_amount
                            ]);
                        } else {
                            $invoice->update([
                                'currency_id' => $customer->currency_id,
                            ]);
                        }
                        $this->items($invoice);
                    });
                }

                if ($customer->expenses()->exists()) {
                    $customer->expenses->map(function ($expense) use ($currency_id) {
                        $expense->update([
                            'currency_id' => $currency_id,
                            'exchange_rate' => 1,
                            'base_amount' => $expense->amount,
                        ]);
                    });
                }

                if ($customer->estimates()->exists()) {
                    $customer->estimates->map(function ($estimate) use ($currency_id, $customer) {
                        if ($customer->currency_id == $currency_id) {
                            $estimate->update([
                                'currency_id' => $currency_id,
                                'exchange_rate' => 1,
                                'base_discount_val' => $estimate->sub_total,
                                'base_sub_total' => $estimate->sub_total,
                                'base_total' => $estimate->total,
                                'base_tax' => $estimate->tax
                            ]);
                        } else {
                            $estimate->update([
                                'currency_id' => $customer->currency_id,
                            ]);
                        }
                        $this->items($estimate);
                    });
                }

                if ($customer->payments()->exists()) {
                    $customer->payments->map(function ($payment) use ($currency_id, $customer) {
                        if ($customer->currency_id == $currency_id) {
                            $payment->update([
                                'currency_id' => $currency_id,
                                'base_amount' => $payment->amount,
                                'exchange_rate' => 1
                            ]);
                        } else {
                            $payment->update([
                                'currency_id' => $customer->currency_id,
                            ]);
                        }
                    });
                }
            }
        }
    }

    public function items($model)
    {
        $model->items->map(function ($item) use ($model) {
            $item->update([
                'exchange_rate' => $model->exchange_rate,
                'base_discount_val' => $item->discount_val * $model->exchange_rate,
                'base_price' => $item->price * $model->exchange_rate,
                'base_tax' => $item->tax * $model->exchange_rate,
                'base_total' => $item->total * $model->exchange_rate
            ]);

            $this->taxes($item, $model->currency_id);
        });

        $this->taxes($model, $model->currency_id);
    }

    public function taxes($model, $currency_id)
    {
        if ($model->taxes()->exists()) {
            $model->taxes->map(function ($tax) use ($model, $currency_id) {
                $tax->update([
                    'currency_id' => $currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'base_amount' => $tax->amount * $model->exchange_rate,
                ]);
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
