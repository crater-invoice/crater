@component('mail::message')
{{ $data['user']['name'] }} @lang('mail_viewed_invoice')

@component('mail::button', ['url' => url('/admin/invoices/'.$data['invoice']['id'].'/view')])
@lang('mail_view_invoice')
@endcomponent

@lang('mail_thanks')<br>
{{ config('app.name') }}
@endcomponent
