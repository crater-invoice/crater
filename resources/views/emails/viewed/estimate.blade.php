@component('mail::message')
{{ $data['user']['name'] }} heeft deze offerte bekeken.

@component('mail::button', ['url' => url('/admin/estimates/'.$data['estimate']['id'].'/view')])
Bekijk offerte
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
