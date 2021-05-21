<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Estimate;
use Illuminate\Console\Command;

class CheckEstimateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:estimates:status';

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
        $status = [Estimate::STATUS_ACCEPTED, Estimate::STATUS_REJECTED, Estimate::STATUS_EXPIRED];
        $estimates = Estimate::whereNotIn('status', $status)->whereDate('expiry_date', '<', $date)->get();

        foreach ($estimates as $estimate) {
            $estimate->status = Estimate::STATUS_EXPIRED;
            printf("Estimate %s is EXPIRED \n", $estimate->estimate_number);
            $estimate->save();
        }
    }
}
