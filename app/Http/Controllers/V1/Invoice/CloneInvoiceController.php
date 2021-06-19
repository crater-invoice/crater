<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Illuminate\Http\Request;

class CloneInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Invoice $invoice)
    {
        $date = Carbon::now();

        $invoice_prefix = CompanySetting::getSetting(
            'invoice_prefix',
            $request->header('company')
        );

        $newInvoice = Invoice::create([
            'invoice_date' => $date->format('Y-m-d'),
            'due_date' => $date->format('Y-m-d'),
            'invoice_number' => $invoice_prefix."-".Invoice::getNextInvoiceNumber($invoice_prefix),
            'reference_number' => $invoice->reference_number,
            'user_id' => $invoice->user_id,
            'company_id' => $request->header('company'),
            'template_name' => 'invoice1',
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'sub_total' => $invoice->sub_total,
            'discount' => $invoice->discount,
            'discount_type' => $invoice->discount_type,
            'discount_val' => $invoice->discount_val,
            'total' => $invoice->total,
            'due_amount' => $invoice->total,
            'tax_per_item' => $invoice->tax_per_item,
            'discount_per_item' => $invoice->discount_per_item,
            'tax' => $invoice->tax,
            'notes' => $invoice->notes,
            'unique_hash' => str_random(60),
        ]);

        $invoice->load('items.taxes');

        $invoiceItems = $invoice->items->toArray();

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $request->header('company');
            $invoiceItem['name'] = $invoiceItem['name'];
            $item = $newInvoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');

                    if ($tax['amount']) {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($invoice->taxes) {
            foreach ($invoice->taxes->toArray() as $tax) {
                $tax['company_id'] = $request->header('company');
                $newInvoice->taxes()->create($tax);
            }
        }

        $newInvoice = Invoice::with([
                'items',
                'user',
                'taxes'
            ])
            ->find($newInvoice->id);

        return response()->json([
            'invoice' => $newInvoice,
            'success' => true,
        ]);
    }
}
