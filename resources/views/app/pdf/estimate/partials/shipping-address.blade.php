@if($estimate->user->shippingaddress)
    <p class="ship-to">Ship To,</p>
    @if($estimate->user->shippingaddress->name)
        <p class="ship-user-name">
            {{$estimate->user->shippingaddress->name}}
        </p>
    @endif
    <p class="ship-user-address">
        @if($estimate->user->shippingaddress->address_street_1)
            {{$estimate->user->shippingaddress->address_street_1}}<br>
        @endif

        @if($estimate->user->shippingaddress->address_street_2)
            {{$estimate->user->shippingaddress->address_street_2}}<br>
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

        @if($estimate->user->phone)
            <p class="ship-user-phone">
                Phone :{{$estimate->user->shippingaddress->phone}}
            </p>
        @endif

    </p>
@endif
