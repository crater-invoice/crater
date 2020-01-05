@if($payment->user->shippingaddress)
    <p class="ship-to">Ship To,</p>
    @if($payment->user->shippingaddress->name)
        <p class="ship-user-name">
            {{$payment->user->shippingaddress->name}}
        </p>
    @endif
    <p class="ship-user-address">
        @if($payment->user->shippingaddress->address_street_1)
            {{$payment->user->shippingaddress->address_street_1}}<br>
        @endif

        @if($payment->user->shippingaddress->address_street_2)
            {{$payment->user->shippingaddress->address_street_2}}<br>
        @endif

        @if($payment->user->shippingaddress->city && $payment->user->shippingaddress->city)
            {{$payment->user->shippingaddress->city}},
        @endif

        @if($payment->user->shippingaddress->state && $payment->user->shippingaddress->state)
            {{$payment->user->shippingaddress->state}}.
        @endif

        @if($payment->user->shippingaddress->zip)
            {{$payment->user->shippingaddress->zip}}<br>
        @endif

        @if($payment->user->shippingaddress->country && $payment->user->shippingaddress->country->name)
            {{$payment->user->shippingaddress->country->name}}<br>
        @endif

        @if($payment->user->phone)
            <p class="ship-user-phone">
                Phone :{{$payment->user->shippingaddress->phone}}
            </p>
        @endif

    </p>
@endif
