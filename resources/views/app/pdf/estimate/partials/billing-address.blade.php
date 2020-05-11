@if($estimate->user->billingaddress)
    <p class="billing-address-label">Bill To,</p>
    @if($estimate->user->billingaddress->name)
        <p class="billing-address-name">
            {{$estimate->user->billingaddress->name}}
        </p>
    @endif
    <p class="billing-address">
        @if($estimate->user->billingaddress->address_street_1)
            {!! nl2br(htmlspecialchars($estimate->user->billingaddress->address_street_1)) !!}<br>
        @endif

        @if($estimate->user->billingaddress->address_street_2)
            {!! nl2br(htmlspecialchars($estimate->user->billingaddress->address_street_2)) !!}<br>
        @endif

        @if($estimate->user->billingaddress->city)
            {{$estimate->user->billingaddress->city}},
        @endif

        @if($estimate->user->billingaddress->state)
            {{$estimate->user->billingaddress->state}}.
        @endif

        @if($estimate->user->billingaddress->zip)
            {{$estimate->user->billingaddress->zip}}<br>
        @endif

        @if($estimate->user->billingaddress->country && $estimate->user->billingaddress->country->name)
            {{$estimate->user->billingaddress->country->name}}<br>
        @endif

        @if($estimate->user->billingaddress->phone)
            <p class="billing-address">
                Phone :{{$estimate->user->billingaddress->phone}}
            </p>
        @endif
    </p>
@endif
