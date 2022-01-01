<?php

namespace Crater\Http\Controllers\V1\Admin\General;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\BulkExchangeRateRequest;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\Tax;

class BulkExchangeRateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(BulkExchangeRateRequest $request)
    {
        $bulkExchangeRate = CompanySetting::getSetting('bulk_exchange_rate_configured', $request->header('company'));

        if ($bulkExchangeRate == 'NO') {
            if ($request->currencies) {
                foreach ($request->currencies as $currency) {
                    $currency['exchange_rate'] = $currency['exchange_rate'] ?? 1;

                    $invoices = Invoice::where('currency_id', $currency['id'])->get();

                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            $invoice->update([
                                'exchange_rate' => $currency['exchange_rate'],
                                'base_discount_val' => $invoice->sub_total * $currency['exchange_rate'],
                                'base_sub_total' => $invoice->sub_total * $currency['exchange_rate'],
                                'base_total' => $invoice->total * $currency['exchange_rate'],
                                'base_tax' => $invoice->tax * $currency['exchange_rate'],
                                'base_due_amount' => $invoice->due_amount * $currency['exchange_rate']
                            ]);

                            $this->items($invoice);
                        }
                    }

                    $estimates = Estimate::where('currency_id', $currency['id'])->get();

                    if ($estimates) {
                        foreach ($estimates as $estimate) {
                            $estimate->update([
                                'exchange_rate' => $currency['exchange_rate'],
                                'base_discount_val' => $estimate->sub_total * $currency['exchange_rate'],
                                'base_sub_total' => $estimate->sub_total * $currency['exchange_rate'],
                                'base_total' => $estimate->total * $currency['exchange_rate'],
                                'base_tax' => $estimate->tax * $currency['exchange_rate']
                            ]);

                            $this->items($estimate);
                        }
                    }

                    $taxes = Tax::where('currency_id', $currency['id'])->get();

                    if ($taxes) {
                        foreach ($taxes as $tax) {
                            $tax->base_amount = $tax->base_amount * $currency['exchange_rate'];
                            $tax->save();
                        }
                    }

                    $payments = Payment::where('currency_id', $currency['id'])->get();

                    if ($payments) {
                        foreach ($payments as $payment) {
                            $payment->exchange_rate = $currency['exchange_rate'];
                            $payment->base_amount = $payment->amount * $currency['exchange_rate'];
                            $payment->save();
                        }
                    }
                }
            }

            $settings = [
                'bulk_exchange_rate_configured' => 'YES'
            ];

            CompanySetting::setSettings($settings, $request->header('company'));

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'error' => false
        ]);
    }

    public function items($model)
    {
        foreach ($model->items as $item) {
            $item->update([
                'exchange_rate' => $model->exchange_rate,
                'base_discount_val' => $item->discount_val * $model->exchange_rate,
                'base_price' => $item->price * $model->exchange_rate,
                'base_tax' => $item->tax * $model->exchange_rate,
                'base_total' => $item->total * $model->exchange_rate
            ]);

            $this->taxes($item);
        }

        $this->taxes($model);
    }

    public function taxes($model)
    {
        if ($model->taxes()->exists()) {
            $model->taxes->map(function ($tax) use ($model) {
                $tax->update([
                    'exchange_rate' => $model->exchange_rate,
                    'base_amount' => $tax->amount * $model->exchange_rate,
                ]);
            });
        }
    }
}
