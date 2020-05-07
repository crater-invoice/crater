@if($invoice->user->shippingaddress)
    <p class="shipping-address-label">Ship To,</p>
    @if($invoice->user->shippingaddress->name)
        <p class="shipping-address-name">
            {{$invoice->user->shippingaddress->name}}
        </p>
    @endif
    <p class="shipping-address">
        @if($invoice->user->shippingaddress->address_street_1)
            {!! nl2br(htmlspecialchars($invoice->user->shippingaddress->address_street_1)) !!}<br>
        @endif

        @if($invoice->user->shippingaddress->address_street_2)
            {!! nl2br(htmlspecialchars($invoice->user->shippingaddress->address_street_2)) !!}<br>
        @endif

        @if($invoice->user->shippingaddress->city && $invoice->user->shippingaddress->city)
            {{$invoice->user->shippingaddress->city}},
        @endif

        @if($invoice->user->shippingaddress->state && $invoice->user->shippingaddress->state)
            {{$invoice->user->shippingaddress->state}}.
        @endif

        @if($invoice->user->shippingaddress->zip)
            {{$invoice->user->shippingaddress->zip}}<br>
        @endif

        @if($invoice->user->shippingaddress->country && $invoice->user->shippingaddress->country->name)
            {{$invoice->user->shippingaddress->country->name}}<br>
        @endif

        @if($invoice->user->shippingaddress->phone)
            <p class="shipping-address">
                Phone :{{$invoice->user->shippingaddress->phone}}
            </p>
        @endif

    </p>
@endif
