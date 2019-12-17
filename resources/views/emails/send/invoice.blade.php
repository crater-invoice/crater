@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => ''])
        @if($data['company']['logo'])
            <img class="header-logo" src="{{asset($data['company']['logo'])}}" alt="{{$data['company']['name']}}">
        @else
            {{$data['company']['name']}}
        @endif
        @endcomponent
    @endslot

    {{-- Body --}}
    <!-- Body here -->

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            <h1>Dear {!! $data['user']['contact_name'] !!}</h1>

<p>Hereby you will find your current invoice {!! $data['invoice']['invoice_number'] !!}.</p><br/>

<p>The invoice amount is {!! format_money_pdf($data['invoice']['due_amount'], $data['user']['currency']) !!}.</p><br/>

<p>To view and print the invoice a PDF reader is required.</p>
@component('mail::button', ['url' => url('/customer/invoices/pdf/'.$data['invoice']['unique_hash'])])
    View Invoice
@endcomponent

<p>If you have any further queries, please let us know.</p><br/>

<h2>Best regards, </h2>

<h2>{{$data['company']['name']}}</h2>
            
        @endcomponent
    @endslot
     {{-- Footer --}}
     @slot('footer')
        @component('mail::footer')
            Powered by <a class="footer-link" href="https://craterapp.com">Crater</a>
        @endcomponent
    @endslot
@endcomponent
