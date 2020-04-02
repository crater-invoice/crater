@component('mail::message')
# Introduction
{{ $data['user']['name'] }} viewed this Estimate.

@component('mail::button', ['url' => url('/admin/estimates/'.$data['estimate']['id'].'/view')])
Estimate
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
