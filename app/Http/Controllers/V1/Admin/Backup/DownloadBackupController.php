<?php

// Implementation taken from nova-backup-tool - https://github.com/spatie/nova-backup-tool/

namespace Crater\Http\Controllers\V1\Admin\Backup;

use Crater\Rules\Backup\PathToZip;
use Illuminate\Http\Request;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadBackupController extends ApiController
{
    public function __invoke(Request $request)
    {
        $this->authorize('manage backups');

        $validated = $request->validate([
            'path' => ['required', new PathToZip()],
        ]);

        $backupDestination = BackupDestination::create(config('filesystems.default'), config('backup.backup.name'));

        $backup = $backupDestination->backups()->first(function (Backup $backup) use ($validated) {
            return $backup->path() === $validated['path'];
        });

        if (! $backup) {
            return response('Backup not found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->respondWithBackupStream($backup);
    }

    public function respondWithBackupStream(Backup $backup): StreamedResponse
    {
        $fileName = pathinfo($backup->path(), PATHINFO_BASENAME);

        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Length' => $backup->size(),
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Pragma' => 'public',
        ];

        return response()->stream(function () use ($backup) {
            $stream = $backup->stream();

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }
}
