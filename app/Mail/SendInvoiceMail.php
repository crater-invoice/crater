<?php
namespace Crater\Mail;

use Config;
use Crater\Models\EmailLog;
use Crater\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoiceMail extends Mailable
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
            'mailable_type' => Invoice::class,
            'mailable_id' => $this->data['invoice']['id']
        ]);
        
        $mailContent = $this->from($this->data['from'])
            ->subject($this->data['subject'])
            ->markdown('emails.send.invoice', ['data', $this->data]);

<<<<<<< HEAD
        if ($this->pdfData) {
            $mailContent->attachData(
                $this->pdfData->output(), 
                $this->data['invoice']['invoice_number'] . '.pdf'
            );
        }
        
        return $mailContent;
=======
        return $this->from($this->data['from'], config('mail.from.name'))
                    ->subject($this->data['subject'])
                    ->markdown('emails.send.invoice', ['data', $this->data]);
>>>>>>> master
    }
}
