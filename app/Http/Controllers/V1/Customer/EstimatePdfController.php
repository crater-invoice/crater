<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\EstimateResource;
use InvoiceShelf\Mail\EstimateViewedMail;
use InvoiceShelf\Models\CompanySetting;
use InvoiceShelf\Models\Customer;
use InvoiceShelf\Models\EmailLog;
use InvoiceShelf\Models\Estimate;
use Illuminate\Http\Request;

class EstimatePdfController extends Controller
{
    public function getPdf(EmailLog $emailLog, Request $request)
    {
        $estimate = Estimate::find($emailLog->mailable_id);

        if (! $emailLog->isExpired()) {
            if ($estimate && ($estimate->status == Estimate::STATUS_SENT || $estimate->status == Estimate::STATUS_DRAFT)) {
                $estimate->status = Estimate::STATUS_VIEWED;
                $estimate->save();
                $notifyEstimateViewed = CompanySetting::getSetting(
                    'notify_estimate_viewed',
                    $estimate->company_id
                );

                if ($notifyEstimateViewed == 'YES') {
                    $data['estimate'] = Estimate::findOrFail($estimate->id)->toArray();
                    $data['user'] = Customer::find($estimate->customer_id)->toArray();
                    $notificationEmail = CompanySetting::getSetting(
                        'notification_email',
                        $estimate->company_id
                    );

                    \Mail::to($notificationEmail)->send(new EstimateViewedMail($data));
                }
            }

            return $estimate->getGeneratedPDFOrStream('estimate');
        }

        abort(403, 'Link Expired.');
    }

    public function getEstimate(EmailLog $emailLog)
    {
        $estimate = Estimate::find($emailLog->mailable_id);

        return new EstimateResource($estimate);
    }
}
