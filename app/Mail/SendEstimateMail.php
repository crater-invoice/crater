<?php

namespace Crater\Mail;

use Crater\Models\EmailLog;
use Crater\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEstimateMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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
            'mailable_type' => Estimate::class,
            'mailable_id' => $this->data['estimate']['id'],
        ]);

        $mailContent = $this->from($this->data['from'], config('mail.from.name'))
                    ->subject($this->data['subject'])
                    ->markdown('emails.send.estimate', ['data', $this->data]);

        if ($this->data['attach']['data']) {
            $mailContent->attachData(
                $this->data['attach']['data']->output(),
                $this->data['estimate']['estimate_number'].'.pdf'
            );
        }

        return $mailContent;
    }
}
