@component('mail::message')
Check your estimate.

@component('mail::button', ['url' => url('/customer/estimates/pdf/'.$data['estimate']['unique_hash'])])
Get your estimate
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
