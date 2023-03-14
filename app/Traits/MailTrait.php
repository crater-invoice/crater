<?php

namespace Crater\Traits;

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
                $mail = new SendInvoiceMail($data);

                break;

            case 'estimate':
                $mail = new SendEstimateMail($data);

                break;

            case 'payment':
                $mail = new SendPaymentMail($data);

                break;
        }

        if ($mailSender->bcc && $mailSender->cc) {
            \Mail::to($data['to'])
                ->bcc(explode(',', $mailSender->bcc))
                ->cc(explode(',', $mailSender->cc))
                ->send($mail);
        }

        if ($mailSender->bcc && $mailSender->cc == null) {
            \Mail::to($data['to'])
                ->bcc(explode(',', $mailSender->bcc))
                ->send($mail);
        }

        if ($mailSender->bcc == null && $mailSender->cc) {
            \Mail::to($data['to'])
                ->cc(explode(',', $mailSender->cc))
                ->send($mail);
        }

        if ($mailSender->bcc == null && $mailSender->cc == null) {
            \Mail::to($data['to'])
                ->send($mail);
        }

        return true;
    }
}
