<footer class="footer">
    <hr/>
    {{$invoice->user->company->name}} -
    @if($company_address->addresses[0]['address_street_1'])
        {{$company_address->addresses[0]['address_street_1']}}
    @endif
    @if(!empty($company_address->addresses[0]['address_street_2']))
        -
        {{$company_address->addresses[0]['address_street_2']}}
    @endif
    -
    @if($company_address->addresses[0]['city'])
        {{$company_address->addresses[0]['city']}}
    @endif
    @if(!empty($company_address->addresses[0]['state']))
        -
        {{$company_address->addresses[0]['state']}}
    @endif
    -
    @if($company_address->addresses[0]['zip'])
        {{$company_address->addresses[0]['zip']}}
    @endif
    -
    @if($company_address->addresses[0]['country'])
        {{$company_address->addresses[0]['country']->name}} <br>
    @endif
    @if($company_address->addresses[0]['phone'])
        {{$company_address->addresses[0]['phone']}}
    @endif
</footer>
