<?php

namespace Crater\Jobs;

use Crater\Invoice;
use Crater\InvoiceBackupFolder;
use Illuminate\Foundation\Bus\Dispatchable;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class BackupInvoiceToGoogleDrive
{
    use Dispatchable;

    private $backupFolder;
    private $invoice;
    private $client;

    public function __construct(InvoiceBackupFolder $backupFolder, Invoice $invoice)
    {
        $this->backupFolder = $backupFolder;
        $this->invoice = $invoice;

        $this->setUpGoogleClient(
            storage_path(config('services.google.service_account_key'))
        );
    }

    public function handle()
    {
        $service = new Google_Service_Drive($this->client);

        $file = new Google_Service_Drive_DriveFile;

        $file->setName(
            sprintf('Invoice - %s.pdf', $this->invoice->invoice_number)
        );

        $file->setParents([$this->backupFolder->folder]);

        $pdf = BuildPDFInvoice::dispatchNow($this->invoice);

        return $service->files->create(
            $file,
            [
                'data' => $pdf->output(),
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart'
            ]
        );
    }

    private function setUpGoogleClient(string $credentials_file)
    {
        $this->client = new Google_Client;
        $this->client->setAuthConfig($credentials_file);
        $this->client->addScope(Google_Service_Drive::DRIVE);
    }
}
