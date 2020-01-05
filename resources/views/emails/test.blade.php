@component('mail::message')
# Test Email

{{ $my_message }}

@component('mail::button', ['url' => ''])
Test
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
