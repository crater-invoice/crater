<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvertEstimateController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Crater\Models\Estimate $estimate
    * @return \Illuminate\Http\Response
    */
    public function __invoke(Request $request, Estimate $estimate)
    {
        $estimate->load(['items', 'items.taxes', 'user', 'taxes']);

        $invoice_date = Carbon::now();
        $due_date = Carbon::now()->addDays(7);

        $invoice_prefix = CompanySetting::getSetting(
            'invoice_prefix',
            $request->header('company')
        );

        $invoice = Invoice::create([
            'creator_id' => Auth::id(),
            'invoice_date' => $invoice_date->format('Y-m-d'),
            'due_date' => $due_date->format('Y-m-d'),
            'invoice_number' => $invoice_prefix."-".Invoice::getNextInvoiceNumber($invoice_prefix),
            'reference_number' => $estimate->reference_number,
            'user_id' => $estimate->user_id,
            'company_id' => $request->header('company'),
            'template_name' => 'invoice1',
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'sub_total' => $estimate->sub_total,
            'discount' => $estimate->discount,
            'discount_type' => $estimate->discount_type,
            'discount_val' => $estimate->discount_val,
            'total' => $estimate->total,
            'due_amount' => $estimate->total,
            'tax_per_item' => $estimate->tax_per_item,
            'discount_per_item' => $estimate->discount_per_item,
            'tax' => $estimate->tax,
            'notes' => $estimate->notes,
            'unique_hash' => str_random(60),
        ]);

        $invoiceItems = $estimate->items->toArray();

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $request->header('company');
            $invoiceItem['name'] = $invoiceItem['name'];
            $item = $invoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');

                    if ($tax['amount']) {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($estimate->taxes) {
            foreach ($estimate->taxes->toArray() as $tax) {
                $tax['company_id'] = $request->header('company');
                unset($tax['estimate_id']);
                $invoice->taxes()->create($tax);
            }
        }

        $invoice = Invoice::with([
            'items',
            'user',
            'taxes'
        ])->find($invoice->id);

        return response()->json([
            'invoice' => $invoice,
        ]);
    }
}
