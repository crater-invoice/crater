<?php

namespace Crater\Jobs;

use Crater\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackupInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice;
    const AVAILABLE_SERVICES = [
        'google_drive' => BackupInvoiceToGoogleDrive::class,
    ];

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $backup_folders = $this->invoice->user->invoiceBackupFolders;

        $backup_folders->each(function ($backupFolder) {
            if (! isset(self::AVAILABLE_SERVICES[$backupFolder->service])) {
                return;
            }

            $job = self::AVAILABLE_SERVICES[$backupFolder->service];

            $job::dispatchNow($backupFolder, $this->invoice);
        });
    }
}
