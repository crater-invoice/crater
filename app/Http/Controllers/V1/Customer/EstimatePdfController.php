<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\EstimateResource;
use Crater\Mail\EstimateViewedMail;
use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Crater\Models\EmailLog;
use Crater\Models\Estimate;
use Crater\Models\MailSender;
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
                    $notificationEmail = CompanySetting::getSetting('notification_email', $estimate->company_id);
                    $mailSender = MailSender::where('company_id', $estimate->company_id)->where('is_default', true)->first();
                    MailSender::setMailConfiguration($mailSender->id);

                    $data['from_address'] = $mailSender->from_address;
                    $data['from_name'] = $mailSender->from_name;
                    $data['user'] = Customer::find($estimate->customer_id)->toArray();
                    $data['estimate'] = Estimate::findOrFail($estimate->id)->toArray();

                    send_mail(new EstimateViewedMail($data), $mailSender, $notificationEmail);
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
