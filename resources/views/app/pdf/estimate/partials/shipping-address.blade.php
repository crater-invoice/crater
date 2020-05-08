@if($estimate->user->shippingaddress)
    <p class="shipping-address-label">Ship To,</p>
    @if($estimate->user->shippingaddress->name)
        <p class="shipping-address-name">
            {{$estimate->user->shippingaddress->name}}
        </p>
    @endif
    <p class="shipping-address">
        @if($estimate->user->shippingaddress->address_street_1)
            {!! nl2br(htmlspecialchars($estimate->user->shippingaddress->address_street_1)) !!}<br>
        @endif

        @if($estimate->user->shippingaddress->address_street_2)
            {!! nl2br(htmlspecialchars($estimate->user->shippingaddress->address_street_2)) !!}<br>
        @endif

        @if($estimate->user->shippingaddress->city && $estimate->user->shippingaddress->city)
            {{$estimate->user->shippingaddress->city}},
        @endif

        @if($estimate->user->shippingaddress->state && $estimate->user->shippingaddress->state)
            {{$estimate->user->shippingaddress->state}}.
        @endif

        @if($estimate->user->shippingaddress->zip)
            {{$estimate->user->shippingaddress->zip}}<br>
        @endif

        @if($estimate->user->shippingaddress->country && $estimate->user->shippingaddress->country->name)
            {{$estimate->user->shippingaddress->country->name}}<br>
        @endif

        @if($estimate->user->shippingAddress->phone)
            <p class="shipping-address">
                Phone :{{$estimate->user->shippingaddress->phone}}
            </p>
        @endif

    </p>
@endif
