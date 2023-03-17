<?php

namespace Crater\Traits;

use Crater\Mail\EstimateViewedMail;
use Crater\Mail\InvoiceViewedMail;
use Crater\Mail\SendEstimateMail;
use Crater\Mail\SendInvoiceMail;
use Crater\Mail\SendPaymentMail;
use Crater\Models\MailSender;

trait MailTrait
{
    public function setMail($model, $data)
    {
        $mailSender = MailSender::setMailConfiguration($data['mail_sender_id'], true);

        $data['from_address'] = $mailSender->from_address;
        $data['from_name'] = $mailSender->from_name;

        switch ($model) {
            case 'invoice':
               send_mail(new SendInvoiceMail($data), $mailSender, $data['to']);

                break;

            case 'estimate':
               send_mail(new SendEstimateMail($data), $mailSender, $data['to']);

                break;

            case 'payment':
               send_mail(new SendPaymentMail($data), $mailSender, $data['to']);

                break;
        }

        return true;
    }
}
