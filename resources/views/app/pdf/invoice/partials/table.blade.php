<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr class="item-table-heading-row">
        <th width="2%" class="item-table-heading text-right pr-20">#</th>
        <th width="40%" class="item-table-heading text-left pl-0">Items</th>
        <th class="item-table-heading text-right pr-20">Quantity</th>
        <th class="item-table-heading pr-20 text-right">Price</th>
        @if($invoice->discount_per_item === 'YES')
        <th class="item-table-heading text-right pl-10">Discount</th>
        @endif
        <th class="item-table-heading text-right">Amount</th>
    </tr>
    @php
        $index = 1
    @endphp
    @foreach ($invoice->items as $item)
        <tr class="item-row">
            <td
                class="item-cell text-right pr-20"
                style="vertical-align: top;"
            >
                {{$index}}
            </td>
            <td
                class="item-cell text-left pl-0"
                style="vertical-align: top;"
            >
                <span>{{ $item->name }}</span><br>
                <span class="item-description">{!! nl2br(htmlspecialchars($item->description)) !!}</span>
            </td>
            <td
                class="item-cell pr-20 text-right"
                style="vertical-align: top;"
            >
                {{$item->quantity}}
            </td>
            <td
                class="item-cell text-right pr-20"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->price, $invoice->user->currency) !!}
            </td>
            @if($invoice->discount_per_item === 'YES')
                <td
                    class="item-cell text-right pl-10"
                    style="vertical-align: top;"
                >
                    @if($item->discount_type === 'fixed')
                        {!! format_money_pdf($item->discount_val, $invoice->user->currency) !!}
                    @endif
                    @if($item->discount_type === 'percentage')
                        {{$item->discount}}%
                    @endif
                </td>
            @endif
            <td
                class="item-cell text-right"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->total, $invoice->user->currency) !!}
            </td>
        </tr>
        @php
            $index += 1
        @endphp
    @endforeach
</table>

<hr class="item-cell-table-hr">

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($invoice->items) > 12) page-break @endif">
        <tr>
            <td class="border-0 total-table-attribute-label">Subtotal</td>
            <td class="border-0 item-cell py-2 total-table-attribute-value">
                {!! format_money_pdf($invoice->sub_total, $invoice->user->currency) !!}
            </td>
        </tr>

        @if ($invoice->tax_per_item === 'YES')
            @for ($i = 0; $i < count($labels); $i++)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$labels[$i]}}
                    </td>
                    <td class="border-0 item-cell py-2 total-table-attribute-value">
                        {!! format_money_pdf($taxes[$i], $invoice->user->currency) !!}
                    </td>
                </tr>
            @endfor
        @else
            @foreach ($invoice->taxes as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$tax->name.' ('.$tax->percent.'%)'}}
                    </td>
                    <td class="border-0 item-cell py-2 total-table-attribute-value">
                        {!! format_money_pdf($tax->amount, $invoice->user->currency) !!}
                    </td>
                </tr>
            @endforeach
        @endif

        @if ($invoice->discount_per_item === 'NO')
            <tr>
                <td class="border-0 total-table-attribute-label">
                    @if($invoice->discount_type === 'fixed')
                        Discount
                    @endif
                    @if($invoice->discount_type === 'percentage')
                        Discount ({{$invoice->discount}}%)
                    @endif
                </td>
                <td class="border-0 item-cell py-2 total-table-attribute-value" >
                    @if($invoice->discount_type === 'fixed')
                        {!! format_money_pdf($invoice->discount_val, $invoice->user->currency) !!}
                    @endif
                    @if($invoice->discount_type === 'percentage')
                        {!! format_money_pdf($invoice->discount_val, $invoice->user->currency) !!}
                    @endif
                </td>
            </tr>
        @endif
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                Total
            </td>
            <td
                class="border-0 total-border-right item-cell py-8 total-table-attribute-value text-primary"
            >
                {!! format_money_pdf($invoice->total, $invoice->user->currency)!!}
            </td>
        </tr>
    </table>
</div>
