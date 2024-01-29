<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Payment;

use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Payment;

class SendPaymentPreviewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Payment $payment)
    {
        $this->authorize('send payment', $payment);

        $markdown = new Markdown(view(), config('mail.markdown'));

        $data = $payment->sendPaymentData($request->all());
        $data['url'] = $payment->paymentPdfUrl;

        return $markdown->render('emails.send.payment', ['data' => $data]);
    }
}
