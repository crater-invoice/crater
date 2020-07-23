@if($payment->user->company)
    <h1 class="company-name"> {{$payment->user->company->name}} </h1>
@endif

@if($company_address)
    <p class="company-address">
        @if($company_address->addresses[0]['address_street_1'])
            {!! nl2br(htmlspecialchars($company_address->addresses[0]['address_street_1'])) !!} <br>
        @endif

        @if($company_address->addresses[0]['address_street_2'])
            {!! nl2br(htmlspecialchars($company_address->addresses[0]['address_street_2'])) !!} <br>
        @endif
        @if($company_address->addresses[0]['city'])
            {{$company_address->addresses[0]['city']}}
        @endif
        @if($company_address->addresses[0]['state'])
            {{$company_address->addresses[0]['state']}}
        @endif
        @if($company_address->addresses[0]['zip'])
            {{$company_address->addresses[0]['zip']}} <br>
        @endif
        @if($company_address->addresses[0]['country'])
            {{$company_address->addresses[0]['country']->name}} <br>
        @endif
        @if($company_address->addresses[0]['phone'])
           {{$company_address->addresses[0]['phone']}} <br>
        @endif
    </p>
@endif
