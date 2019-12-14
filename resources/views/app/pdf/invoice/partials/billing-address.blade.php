@if($invoice->user->billingaddress)
    <p class="bill-to">Bill To,</p>
    @if($invoice->user->billingaddress->name)
        <p class="bill-user-name">
            {{$invoice->user->billingaddress->name}}
        </p>
    @endif
    <p class="bill-user-address">
        @if($invoice->user->billingaddress->address_street_1)
            {{$invoice->user->billingaddress->address_street_1}}<br>
        @endif
        @if($invoice->user->billingaddress->address_street_2)
            {{$invoice->user->billingaddress->address_street_2}}<br>
        @endif
        @if($invoice->user->billingaddress->city && $invoice->user->billingaddress->city)
            {{$invoice->user->billingaddress->city}},
        @endif
        @if($invoice->user->billingaddress->state && $invoice->user->billingaddress->state)
            {{$invoice->user->billingaddress->state}}.
        @endif
        @if($invoice->user->billingaddress->zip)
            {{$invoice->user->billingaddress->zip}}<br>
        @endif
        @if($invoice->user->billingaddress->country && $invoice->user->billingaddress->country->name)
            {{$invoice->user->billingaddress->country->name}}<br>
        @endif
        @if($invoice->user->billingaddress->phone)
            <p class="bill-user-phone">
                Phone :{{$invoice->user->billingaddress->phone}}
            </p>
        @endif
    </p>
@endif
