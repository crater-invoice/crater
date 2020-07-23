@if($payment->user->billingaddress)
    <p class="billing-address-label">Received From:</p>
    @if($payment->user->billingaddress->name)
        <p class="billing-address-name">
            {{$payment->user->billingaddress->name}}
        </p>
    @endif
    <p class="billing-address">
        @if($payment->user->billingaddress->address_street_1)
            {!! nl2br(htmlspecialchars($payment->user->billingaddress->address_street_1)) !!}<br>
        @endif
        @if($payment->user->billingaddress->address_street_2)
            {!! nl2br(htmlspecialchars($payment->user->billingaddress->address_street_2)) !!}<br>
        @endif
        @if($payment->user->billingaddress->city && $payment->user->billingaddress->city)
            {{$payment->user->billingaddress->city}},
        @endif
        @if($payment->user->billingaddress->state && $payment->user->billingaddress->state)
            {{$payment->user->billingaddress->state}}.
        @endif
        @if($payment->user->billingaddress->zip)
            {{$payment->user->billingaddress->zip}}<br>
        @endif
        @if($payment->user->billingaddress->country && $payment->user->billingaddress->country->name)
            {{$payment->user->billingaddress->country->name}}<br>
        @endif
        @if($payment->user->billingaddress->phone)
            <p class="billing-address">
                Phone :{{$payment->user->billingaddress->phone}}
            </p>
        @endif
    </p>
@endif
