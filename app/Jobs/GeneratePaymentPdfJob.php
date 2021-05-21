<?php

namespace Crater\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePaymentPdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $payment;

    public $deleteExistingFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payment, $deleteExistingFile = false)
    {
        $this->payment = $payment;
        $this->deleteExistingFile = $deleteExistingFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->payment->generatePDF('payment', $this->payment->payment_number, $this->deleteExistingFile);

        return 0;
    }
}
