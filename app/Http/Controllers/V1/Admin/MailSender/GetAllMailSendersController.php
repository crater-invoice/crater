<?php

namespace Crater\Http\Controllers\V1\Admin\MailSender;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\MailSenderResource;
use Crater\Models\MailSender;
use Illuminate\Http\Request;

class GetAllMailSendersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $mailSenders = MailSender::whereCompany()->get();

        return MailSenderResource::collection($mailSenders);
    }
}
