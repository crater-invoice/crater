<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Estimate;

use Illuminate\Mail\Markdown;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\SendEstimatesRequest;
use InvoiceShelf\Models\Estimate;

class SendEstimatePreviewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendEstimatesRequest $request, Estimate $estimate)
    {
        $this->authorize('send estimate', $estimate);

        $markdown = new Markdown(view(), config('mail.markdown'));

        $data = $estimate->sendEstimateData($request->all());
        $data['url'] = $estimate->estimatePdfUrl;

        return $markdown->render('emails.send.estimate', ['data' => $data]);
    }
}
