<?php
namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EstimatePdf extends Mailable
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
        $company = $this->data['company']['name'];
        return $this->from($this->notificationEmail)
                    ->subject("Estimate from $company")
                    ->markdown('emails.send.estimate', ['data', $this->data]);
    }
}
