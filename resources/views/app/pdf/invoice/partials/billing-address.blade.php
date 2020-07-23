@if($invoice->user->billingaddress)
    <p class="billing-address-label">Bill To,</p>
    @if($invoice->user->billingaddress->name)
        <p class="billing-address-name">
            {{$invoice->user->billingaddress->name}}
        </p>
    @endif
    <p class="billing-address">
        @if($invoice->user->billingaddress->address_street_1)
            {!! nl2br(htmlspecialchars($invoice->user->billingaddress->address_street_1)) !!}<br>
        @endif
        @if($invoice->user->billingaddress->address_street_2)
            {!! nl2br(htmlspecialchars($invoice->user->billingaddress->address_street_2)) !!}<br>
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
            <p class="billing-address">
                Phone :{{$invoice->user->billingaddress->phone}}
            </p>
        @endif
    </p>
@endif
