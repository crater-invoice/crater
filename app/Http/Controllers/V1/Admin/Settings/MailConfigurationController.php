<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TestMailDriverRequest;
use Crater\Mail\TestMail;
use Crater\Models\MailSender;
use Illuminate\Http\Request;
use Mail;

class MailConfigurationController extends Controller
{
    public function TestMailDriver(TestMailDriverRequest $request)
    {
        $this->authorize('manage email config');

        MailSender::setMailConfiguration($request->mail_sender_id);

        Mail::to($request->to)->send(new TestMail($request->subject, $request->message));

        return response()->json([
            'success' => true,
        ]);
    }

    public function getMailDrivers(Request $request)
    {
        $drivers = [
            'smtp',
            'mail',
            'sendmail',
            'mailgun',
            'ses',
        ];

        return response()->json($drivers);
    }
}
