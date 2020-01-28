<?php

namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentPdf extends Mailable
{
    use Queueable, SerializesModels;

    public $data = [];

    public $notificationEmail = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $notificationEmail)
    {
        $this->data = $data;
        $this->notificationEmail = $notificationEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->notificationEmail)->markdown('emails.send.payment', ['data', $this->data]);
    }
}
