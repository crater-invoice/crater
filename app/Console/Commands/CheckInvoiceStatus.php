<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Invoice;
use Illuminate\Console\Command;

class CheckInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoices:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoices status.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::now();
        $invoices = Invoice::where('status', '<>', Invoice::STATUS_COMPLETED)->whereDate('due_date', '<', $date)->get();

        foreach ($invoices as $invoice) {
            $invoice->status = Invoice::STATUS_OVERDUE;
            printf("Invoice %s is OVERDUE \n", $invoice->invoice_number);
            $invoice->save();
        }
    }
}
