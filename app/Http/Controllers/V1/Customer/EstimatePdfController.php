<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\EstimateResource;
use Crater\Mail\EstimateViewedMail;
use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Crater\Models\EmailLog;
use Crater\Models\Estimate;
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
