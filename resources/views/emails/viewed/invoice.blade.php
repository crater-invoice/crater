@component('mail::message')
# Introduction
Customer viewed this Invoice.

@component('mail::button', ['url' => url('/admin/invoices/'.$data['invoice']['id'].'/view')])
Invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
