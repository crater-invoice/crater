<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\Customer\InvoiceResource as CustomerInvoiceResource;
use Crater\Mail\InvoiceViewedMail;
use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Crater\Models\EmailLog;
use Crater\Models\Invoice;
use Crater\Models\MailSender;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function getPdf(EmailLog $emailLog, Request $request)
    {
        $invoice = Invoice::find($emailLog->mailable_id);

        if (! $emailLog->isExpired()) {
            if ($invoice && ($invoice->status == Invoice::STATUS_SENT || $invoice->status == Invoice::STATUS_DRAFT)) {
                $invoice->status = Invoice::STATUS_VIEWED;
                $invoice->viewed = true;
                $invoice->save();
                $notifyInvoiceViewed = CompanySetting::getSetting(
                    'notify_invoice_viewed',
                    $invoice->company_id
                );

                if ($notifyInvoiceViewed == 'YES') {
                    $notificationEmail = CompanySetting::getSetting('notification_email', $invoice->company_id);
                    $mailSender = MailSender::where('company_id', $invoice->company_id)->where('is_default', true)->first();
                    MailSender::setMailConfiguration($mailSender->id);

                    $data['from_address'] = $mailSender->from_address;
                    $data['from_name'] = $mailSender->from_name;
                    $data['invoice'] = Invoice::findOrFail($invoice->id)->toArray();
                    $data['user'] = Customer::find($invoice->customer_id)->toArray();

                    send_mail(new InvoiceViewedMail($data), $mailSender, $notificationEmail);
                }
            }

            if ($request->has('pdf')) {
                return $invoice->getGeneratedPDFOrStream('invoice');
            }

            return view('app')->with([
                'customer_logo' => get_company_setting('customer_portal_logo', $invoice->company_id),
                'current_theme' => get_company_setting('customer_portal_theme', $invoice->company_id)
            ]);
        }

        abort(403, 'Link Expired.');
    }

    public function getInvoice(EmailLog $emailLog)
    {
        $invoice = Invoice::find($emailLog->mailable_id);

        return new CustomerInvoiceResource($invoice);
    }
}
