<?php

namespace Crater\Http\Controllers\V1\Admin\MailSender;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\MailSenderRequest;
use Crater\Http\Resources\MailSenderResource;
use Crater\Models\MailSender;
use Illuminate\Http\Request;

class MailSenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MailSender::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $mailSenders = MailSender::whereCompany()
            ->applyFilters($request->all())
            ->paginateData($limit);

        return (MailSenderResource::collection($mailSenders))
            ->additional(['meta' => [
                'mail_sender_total_count' => MailSender::whereCompany()->count(),
            ]]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MailSenderRequest $request)
    {
        $this->authorize('create', MailSender::class);

        $mailConfiguration = MailSender::where('company_id', $request->header('company'))
            ->where('is_default', true)
            ->count();

        if ($mailConfiguration > 0 && $request['is_default'] == true) {
            return respondJson('default_record_exists', 'Default mail sender already exist');
        }

        $mailSender = MailSender::createFromRequest($request);

        return new MailSenderResource($mailSender);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\SenderMail  $senderMail
     * @return \Illuminate\Http\Response
     */
    public function show(MailSender $mailSender)
    {
        $this->authorize('view', $mailSender);

        return new MailSenderResource($mailSender);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\SenderMail  $senderMail
     * @return \Illuminate\Http\Response
     */
    public function update(MailSenderRequest $request, MailSender $mailSender)
    {
        $this->authorize('update', $mailSender);

        $mailConfiguration = MailSender::where('company_id', $request->header('company'))
            ->where('is_default', true)
            ->where('id', '<>', $mailSender->id)
            ->count();

        if ($mailConfiguration > 0 && $request['is_default'] == true) {
            return respondJson('default_record_exists', 'Default mail sender already exist');
        }

        $mailSender->updateFromRequest($request);

        return new MailSenderResource($mailSender);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\SenderMail  $senderMail
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailSender $mailSender)
    {
        $this->authorize('delete', $mailSender);

        $mailSender->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
