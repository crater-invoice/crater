<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\PaymentResource;
use InvoiceShelf\Models\EmailLog;
use InvoiceShelf\Models\Payment;
use Illuminate\Http\Request;

class PaymentPdfController extends Controller
{
    public function getPdf(EmailLog $emailLog, Request $request)
    {
        if (! $emailLog->isExpired()) {
            return $emailLog->mailable->getGeneratedPDFOrStream('payment');
        }

        abort(403, 'Link Expired.');
    }

    public function getPayment(EmailLog $emailLog)
    {
        $payment = Payment::find($emailLog->mailable_id);

        return new PaymentResource($payment);
    }
}
