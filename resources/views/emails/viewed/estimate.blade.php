@component('mail::message')
{{ $data['user']['name'] }} @lang('mail_viewed_estimate')

@component('mail::button', ['url' => url('/admin/estimates/'.$data['estimate']['id'].'/view')])
@lang('mail_view_estimate')
@endcomponent

@lang('mail_thanks')<br>
{{ config('app.name') }}
@endcomponent
