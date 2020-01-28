<table width="100%" class="table2" cellspacing="0" border="0">
    <tr class="main-table-header">
        <th width="2%" class="ItemTableHeader" style="text-align: right; color: #55547A; padding-right: 20px">#</th>
        <th width="40%" class="ItemTableHeader" style="text-align: left; color: #55547A; padding-left: 0px">Items</th>
        <th class="ItemTableHeader" style="text-align: right; color: #55547A; padding-right: 20px">Quantity</th>
        <th class="ItemTableHeader" style="text-align: right; color: #55547A; padding-right: 20px">Price</th>
        @if($estimate->discount_per_item === 'YES')
        <th class="ItemTableHeader" style="text-align: right; color: #55547A; padding-left: 10px">Discount</th>
        @endif
        <th class="ItemTableHeader" style="text-align: right; color: #55547A;">Amount</th>
    </tr>
    @php
        $index = 1
    @endphp
    @foreach ($estimate->items as $item)
        <tr class="item-details">
            <td
                class="inv-item items"
                style="text-align: right; color: #040405; padding-right: 20px; vertical-align: top;"
            >
                {{$index}}
            </td>
            <td
                class="inv-item items"
                style="text-align: left; color: #040405;padding-left: 0px"
            >
                <span>{{ $item->name }}</span><br>
                <span
                    style="text-align: left; color: #595959; font-size: 9px; font-weight:300; line-height: 12px;"
                >
                    {!! nl2br(htmlspecialchars($item->description)) !!}
                </span>
            </td>
            <td
                class="inv-item items"
                style="text-align: right; color: #040405; padding-right: 20px"
            >
                {{$item->quantity}}
            </td>
            <td
                class="inv-item items"
                style="text-align: right; color: #040405; padding-right: 20px"
            >
                {!! format_money_pdf($item->price, $estimate->user->currency) !!}
            </td>
            @if($estimate->discount_per_item === 'YES')
                <td class="inv-item items" style="text-align: right; color: #040405; padding-left: 10px">
                    @if($item->discount_type === 'fixed')
                        {!! format_money_pdf($item->discount_val, $estimate->user->currency) !!}
                    @endif
                    @if($item->discount_type === 'percentage')
                        {{$item->discount}}%
                    @endif
                </td>
            @endif
            <td class="inv-item items" style="text-align: right; color: #040405;">
                {!! format_money_pdf($item->total, $estimate->user->currency) !!}
            </td>
        </tr>
        @php
            $index += 1
        @endphp
    @endforeach
</table>

<hr class="items-table-hr">

<table width="100%" cellspacing="0px" style="margin-left:420px;margin-top: 10px" border="0" class="table3 @if(count($estimate->items) > 12) page-break @endif">
    <tr>
        <td class="no-borde" style="color: #55547A; padding-left:10px;  font-size:12px;">Subtotal</td>
        <td class="no-border items"
            style="padding-right:10px; text-align: right;  font-size:12px; color: #040405; font-weight: 500;">{!! format_money_pdf($estimate->sub_total, $estimate->user->currency) !!}</td>
    </tr>

    @if ($estimate->tax_per_item === 'YES')
        @for ($i = 0; $i < count($labels); $i++)
            <tr>
                <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px;  color: #55547A;">
                    {{$labels[$i]}}
                </td>
                <td class="no-border items padd2" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px;  color: #040405">
                    {!! format_money_pdf($taxes[$i], $estimate->user->currency) !!}
                </td>
            </tr>
        @endfor
    @else
        @foreach ($estimate->taxes as $tax)
            <tr>
                <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px;  color: #55547A;">
                    {{$tax->name.' ('.$tax->percent.'%)'}}
                </td>
                <td class="no-border items padd2" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px;  color: #040405">
                    {!! format_money_pdf($tax->amount, $estimate->user->currency) !!}
                </td>
            </tr>
        @endforeach
    @endif

    @if ($estimate->discount_per_item === 'NO')
        <tr>
            <td class="no-border" style="padding-left:10px; text-align:left; font-size:12px; color: #55547A;">
                @if($estimate->discount_type === 'fixed')
                    Discount
                @endif
                @if($estimate->discount_type === 'percentage')
                    Discount ({{$estimate->discount}}%)
                @endif
            </td>
            <td class="no-border items padd2" style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px;  color: #040405">
                @if($estimate->discount_type === 'fixed')
                    {!! format_money_pdf($estimate->discount_val, $estimate->user->currency) !!}
                @endif
                @if($estimate->discount_type === 'percentage')
                    {!! format_money_pdf($estimate->discount_val, $estimate->user->currency) !!}
                @endif
            </td>
        </tr>
    @endif
    <tr>
        <td style="padding:3px 0px"></td>
        <td style="padding:3px 0px"></td>
    </tr>
    <tr>
        <td class="no-border total-border-left"
            style="padding-left:10px; padding-bottom:10px; text-align:left; padding-top:20px; font-size:12px;  color: #55547A;"
        >
            <label class="total-bottom"> Total </label>
        </td>
        <td
            class="no-border total-border-right items padd8"
            style="padding-right:10px; font-weight: 500; text-align: right; font-size:12px;  padding-top:20px; color: #5851DB"
        >
            {!! format_money_pdf($estimate->total, $estimate->user->currency)!!}
        </td>
    </tr>
</table>
