<table width="100%" class="table2" cellspacing="0" border="0">
    <tr class="main-table-header">
        <td width="40%" class="ItemTableHeader" style="text-align: left; color: #55547A; padding-left: 0px">Items</td>
        <td width="17%" class="ItemTableHeader" style="text-align: right; color: #55547A; padding-right: 20px">Quantity</td>
        <td width="18%" class="ItemTableHeader" style="text-align: left; color: #55547A; padding-left: 40px">Price</td>
        <td width="10%" class="ItemTableHeader" style="text-align: left; color: #55547A; padding-left: 10px">Discount</td>
        <td width="15%" class="ItemTableHeader" style="text-align: right; color: #55547A;">Amount</td>
    </tr>
    @foreach ($estimate->items as $item)
        <tr class="item-details">
            <td width="40%" class="inv-item items" style="text-align: left; color: #040405;padding-left: 0px">
                <span>{{ $item->name }}</span><br>
                <span style="text-align: left; color: #595959; font-size: 9px; font-weight:300; line-height: 12px;">{{ $item->description }}</span>
            </td>
            <td width="17%" class="inv-item items" style="text-align: right; color: #040405; padding-right: 20px">{{$item->quantity}}</td>
            <td width="18%" class="inv-item items" style="text-align: left; color: #040405; padding-left: 40px">{{$item->price/100}}</td>
            {{-- <td width="10%" class="inv-item items" style="text-align: left; color: #040405; padding-left: 10px">{{$item->discount/100}}%</td> --}}
            <td width="10%" class="inv-item items" style="text-align: left; color: #040405; padding-left: 10px">
                @if($item->discount_type === 'fixed')
                    {{$item->discount_val/100}}
                @endif
                @if($item->discount_type === 'percentage')
                    {{$item->discount}}%
                @endif
            </td>
            <td width="15%" class="inv-item items" style="text-align: right; color: #040405">{{$item->total/100}}</td>
        </tr>
    @endforeach
</table>

<table width="100%" cellspacing="0px" border="0" class="text-per-item-table3 @if(count($estimate->items) > 12) page-break @endif">
    <tr>
        <td class="no-borde" style="color: #55547A; padding-left:10px; line-height: 18px; font-size:12px;">Subtotal</td>
        <td class="no-border items"
            style="padding-right:10px; text-align: right; line-height: 18px; font-size:12px; color: #040405; font-weight: 500;">{!! format_money_pdf($estimate->sub_total) !!}</td>
    </tr>

    @if ($estimate->tax_per_item === 'YES')
        @for ($i = 0; $i < count($labels); $i++)
            <tr>
                <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px; line-height: 18px; color: #55547A;">
                    {{$labels[$i]}}
                </td>
                <td class="no-border items padd8" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px; line-height: 18px; color: #040405">
                    {!! format_money_pdf($taxes[$i]) !!}
                </td>
            </tr>
        @endfor
    @else
        @foreach ($estimate->taxes as $tax)
            <tr>
                <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px; line-height: 18px; color: #55547A;">
                    {{$tax->name.' ('.$tax->percent.'%)'}}
                </td>
                <td class="no-border items padd8" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px; line-height: 18px; color: #040405">
                    {!! format_money_pdf($tax->amount) !!}
                </td>
            </tr>
        @endforeach
    @endif

    @if ($estimate->discount_per_item === 'NO')
        <tr>
            <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px; color: #55547A;">
                Discount
            </td>
            <td class="no-border items padd8" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px;  color: #040405">
                @if($estimate->discount_type === 'fixed')
                    {!! format_money_pdf($estimate->discount_val) !!}
                @endif
                @if($estimate->discount_type === 'percentage')
                {{$estimate->discount}}% ({!! format_money_pdf($estimate->discount_val) !!})
                @endif
            </td>
        </tr>
    @endif
    <tr >
        <td class="no-border total-border-left"
            style="padding-left:10px; padding-bottom:10px; text-align:left; padding-top:20px; font-size:12px; line-height: 18px; color: #55547A;"
        >
            <label class="total-bottom"> Total </label>
        </td>
        <td
            class="no-border total-border-right items padd8"
            style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px; line-height: 18px; padding-top:20px; color: #5851DB"
        >
        {!! format_money_pdf($estimate->total)!!}
        </td>
    </tr>
</table>
