@if($payment->user->billingaddress)
    <p class="bill-to">Received From:</p>
    @if($payment->user->billingaddress->name)
        <p class="bill-user-name">
            {{$payment->user->billingaddress->name}}
        </p>
    @endif
    <p class="bill-user-address">
        @if($payment->user->billingaddress->address_street_1)
            {{$payment->user->billingaddress->address_street_1}}<br>
        @endif
        @if($payment->user->billingaddress->address_street_2)
            {{$payment->user->billingaddress->address_street_2}}<br>
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
            <p class="bill-user-phone">
                Phone :{{$payment->user->billingaddress->phone}}
            </p>
        @endif
    </p>
@endif
