<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr class="item-table-heading-row">
        <th width="2%" class="pr-20 text-right item-table-heading">#</th>
        <th width="40%" class="pl-0 text-left item-table-heading">@lang('pdf_items_label')</th>
        @foreach($customFields as $field)
            <th class="text-right item-table-heading">{{ $field->label }}</th>
        @endforeach
        <th class="pr-20 text-right item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_price_label')</th>
        @if($estimate->discount_per_item === 'YES')
        <th class="pl-10 text-right item-table-heading">@lang('pdf_discount_label')</th>
        @endif
        <th class="text-right item-table-heading">@lang('pdf_amount_label')</th>
    </tr>
    @php
        $index = 1
    @endphp
    @foreach ($estimate->items as $item)
        <tr class="item-row">
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{$index}}
            </td>
            <td
                class="pl-0 text-left item-cell"
            >
                <span>{{ $item->name }}</span><br>
                <span
                    class="item-description"
                >
                    {!! nl2br(htmlspecialchars($item->description)) !!}
                </span>
            </td>
            @foreach($customFields as $field)
                <td class="text-right item-cell" style="vertical-align: top;">
                    {{ $item->getCustomFieldValueBySlug($field->slug) }}
                </td>
            @endforeach
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{$item->quantity}} @if($item->unit_name) {{$item->unit_name}} @endif
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->price, $estimate->customer->currency) !!}
            </td>
            @if($estimate->discount_per_item === 'YES')
                <td class="pl-10 text-right item-cell" style="vertical-align: top;">
                    @if($item->discount_type === 'fixed')
                        {!! format_money_pdf($item->discount_val, $estimate->customer->currency) !!}
                    @endif
                    @if($item->discount_type === 'percentage')
                        {{$item->discount}}%
                    @endif
                </td>
            @endif
            <td class="text-right item-cell" style="vertical-align: top;">
                {!! format_money_pdf($item->total, $estimate->customer->currency) !!}
            </td>
        </tr>
        @php
            $index += 1
        @endphp
    @endforeach
</table>

<hr class="item-cell-table-hr">

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($estimate->items) > 12) page-break @endif">
        <tr>
            <td class="border-0 total-table-attribute-label">@lang('pdf_subtotal')</td>
            <td class="border-0 item-cell total-table-attribute-value ">{!! format_money_pdf($estimate->sub_total, $estimate->customer->currency) !!}</td>
        </tr>

        @if($estimate->discount > 0)
            @if ($estimate->discount_per_item === 'NO')
                <tr>
                    <td class="pl-10 border-0 total-table-attribute-label">
                        @if($estimate->discount_type === 'fixed')
                            @lang('pdf_discount_label')
                        @endif
                        @if($estimate->discount_type === 'percentage')
                            @lang('pdf_discount_label') ({{$estimate->discount}}%)
                        @endif
                    </td>
                    <td class="text-right border-0 item-cell total-table-attribute-value">
                        @if($estimate->discount_type === 'fixed')
                            {!! format_money_pdf($estimate->discount_val, $estimate->customer->currency) !!}
                        @endif
                        @if($estimate->discount_type === 'percentage')
                            {!! format_money_pdf($estimate->discount_val, $estimate->customer->currency) !!}
                        @endif
                    </td>
                </tr>
            @endif
        @endif
        
        @if ($estimate->tax_per_item === 'YES')
            @foreach ($taxes as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$tax->name.' ('.$tax->percent.'%)'}}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {!! format_money_pdf($tax->amount, $estimate->customer->currency) !!}
                    </td>
                </tr>
            @endforeach
        @else
            @foreach ($estimate->taxes as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$tax->name.' ('.$tax->percent.'%)'}}
                    </td>
                    <td class="border-0 item-cell total-table-attribute-value" >
                        {!! format_money_pdf($tax->amount, $estimate->customer->currency) !!}
                    </td>
                </tr>
            @endforeach
        @endif
        
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">@lang('pdf_total')</td>
            <td class="py-8 border-0 total-border-right item-cell total-table-attribute-value" style="color: #5851D8">
                {!! format_money_pdf($estimate->total, $estimate->customer->currency)!!}
            </td>
        </tr>
    </table>
</div>
