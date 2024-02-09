@component('mail::message')
{{ $data['user']['name'] }} heeft deze factuur bekeken.

@component('mail::button', ['url' => url('/admin/invoices/'.$data['invoice']['id'].'/view')])
Bekijk factuur
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
