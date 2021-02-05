<?php

namespace Crater\Mail;

use Crater\Models\EmailLog;
use Crater\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data = [];
    public $pdfData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $pdfData)
    {
        $this->data = $data;
        $this->pdfData = $pdfData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        EmailLog::create([
            'from' => $this->data['from'],
            'to' => $this->data['to'],
            'subject' => $this->data['subject'],
            'body' => $this->data['body'],
            'mailable_type' => Payment::class,
            'mailable_id' => $this->data['payment']['id']
        ]);

        $mailContent = $this->from($this->data['from'])
            ->subject($this->data['subject'])
            ->markdown('emails.send.payment', ['data', $this->data]);
        
        if ($this->pdfData) {
            $mailContent->attachData(
                $this->pdfData->output(), 
                $this->data['payment']['payment_number'] . '.pdf'
            );
        }
        
        return $mailContent;
    }
}
