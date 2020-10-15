<?php

namespace Crater\Jobs;

use PDF;
use Crater\Company;
use Crater\CompanySetting;
use Crater\Invoice;
use Crater\InvoiceTemplate;
use Crater\User;
use Illuminate\Foundation\Bus\Dispatchable;

class BuildPDFInvoice
{
    use Dispatchable;

    private $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $taxTypes = [];
        $taxes = [];
        $labels = [];

        if ($this->invoice->tax_per_item === 'YES') {
            foreach ($this->invoice->items as $item) {
                foreach ($item->taxes as $tax) {
                    if (!in_array($tax->name, $taxTypes)) {
                        array_push($taxTypes, $tax->name);
                        array_push($labels, $tax->name.' ('.$tax->percent.'%)');
                    }
                }
            }

            foreach ($taxTypes as $taxType) {
                $total = 0;

                foreach ($this->invoice->items as $item) {
                    foreach ($item->taxes as $tax) {
                        if($tax->name == $taxType) {
                            $total += $tax->amount;
                        }
                    }
                }

                array_push($taxes, $total);
            }
        }

        $invoiceTemplate = InvoiceTemplate::find($this->invoice->invoice_template_id);
        $company = Company::find($this->invoice->company_id);
        $companyAddress = User::with(['addresses', 'addresses.country'])->find(1);

        $logo = $company->getMedia('logo')->first();

        if($logo) {
            $logo = $logo->getFullUrl();
        }

        $colors = [
            'invoice_primary_color',
            'invoice_column_heading',
            'invoice_field_label',
            'invoice_field_value',
            'invoice_body_text',
            'invoice_description_text',
            'invoice_border_color'
        ];
        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($this->invoice->company_id)
            ->get();

        view()->share([
            'invoice' => $this->invoice,
            'company_address' => $companyAddress,
            'logo' => $logo ?? null,
            'colors' => $colorSettings,
            'labels' => $labels,
            'taxes' => $taxes
        ]);

        return PDF::loadView('app.pdf.invoice.'.$invoiceTemplate->view);
    }
}
