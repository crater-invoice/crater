<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Models\FileDisk;
use Crater\Models\MailSender;

class ConfigMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Storage::disk('local')->has('database_created')) {
            if ($request->has('file_disk_id')) {
                $file_disk = FileDisk::find($request->file_disk_id);
            } else {
                $file_disk = FileDisk::whereSetAsDefault(true)->first();
            }

            if ($file_disk) {
                $file_disk->setConfig();
            }
        }

        $default_mail_sender = MailSender::where('company_id', $request->header('company'))->where('is_default', true)->first();

        if ($default_mail_sender) {
            $default_mail_sender->setMailConfiguration($default_mail_sender->id);
        }

        return $next($request);
    }
}
