@component('mail::message')
@if (! empty($logo))
# {{ $company }}
@else
@component('mail::button', ['url' => url('/customer/invoices/pdf/'.$data['invoice']['unique_hash'])])
Get your invoice
@endcomponent

{{-- Thanks,<br>
{{ config('app.name') }} --}}
@endcomponent
