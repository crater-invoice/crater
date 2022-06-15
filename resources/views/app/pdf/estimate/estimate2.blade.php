<!DOCTYPE html>
<html>

<head>
    <title>@lang('pdf_estimate_label') - {{ $estimate->estimate_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        /* -- Base -- */
        body {
            font-family: "DejaVu Sans";
        }

        html {
            margin: 0px;
            padding: 0px;
            margin-top: 50px;
        }

        table {
            border-collapse: collapse;
        }

        hr {
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-container {
            background: #817AE3;
            position: absolute;
            width: 100%;
            height: 141px;
            left: 0px;
            top: -60px;
        }

        .header-section-left {
            padding-top: 45px;
            padding-bottom: 45px;
            padding-left: 30px;
            display: inline-block;
            width: 30%;
        }

        .header-logo {
            position: absolute;

            text-transform: capitalize;
            color: #fff;
        }

        .header-section-right {
            display: inline-block;
            width: 35%;
            float: right;
            padding: 20px 30px 20px 0px;
            text-align: right;
            color: white;
        }

        .header {
            font-size: 20px;
            color: rgba(0, 0, 0, 0.7);
        }

        /* -- Estimate Details -- */

        .estimate-details-container {
            text-align: center;
            width: 40%;
        }

        .estimate-details-container h1 {
            margin: 0;
            font-size: 24px;
            line-height: 36px;
            text-align: right;
            font-family: "DejaVu Sans";
        }

        .estimate-details-container h4 {
            margin: 0;
            font-size: 10px;
            line-height: 15px;
            text-align: right;
        }

        .estimate-details-container h3 {
            margin-bottom: 1px;
            margin-top: 0;
        }

        /* -- Address -- */

        .content-wrapper {
            display: block;
            margin-top: 60px;
            padding-bottom: 20px;
        }

        .address-container {
            display: block;
            padding-top: 20px;
            margin-top: 10px;
        }

        /* -- Company Address -- */

        .company-address-container {
            padding: 0 0 0 30px;
            display: inline;
            float: left;
            width: 30%;
        }

        .company-address-container {
            padding-left: 30px;
            float: left;
            width: 30%;
            text-transform: capitalize;
            margin-bottom: 2px;
        }

        .company-address-container h1 {
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
        }

        .company-address {
            margin-top: 2px;
            text-align: left;
            word-wrap: break-word;
            font-size: 12px;
            line-height: 15px;
            color: #595959;
        }

        /* -- Billing -- */

        .billing-address-container {
            display: block;
            /* position: absolute; */
            float: right;
            padding: 0 40px 0 0;
        }

        .billing-address-label {
            font-size: 12px;
            line-height: 18px;
            padding: 0px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            max-width: 160px;
            font-size: 15px;
            line-height: 22px;
            padding: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .billing-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            width: 170px;
            word-wrap: break-word;
        }

        /* -- Shipping -- */

        .shipping-address-container {
            display: block;
            float: right;
            padding: 0 30px 0 0;
        }

        .shipping-address-label {
            font-size: 12px;
            line-height: 18px;
            padding: 0px;
            margin-bottom: 0px;
        }

        .shipping-address-name {
            max-width: 160px;
            font-size: 15px;
            line-height: 22px;
            padding: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .shipping-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px 30px 0px 20px;
            margin: 0px;
            width: 170px;
            word-wrap: break-word;
        }

        .attribute-label {
            font-size: 12;
            font-weight: bold;
            line-height: 22px;
            color: rgba(0, 0, 0, 0.8);
        }

        .attribute-value {
            font-size: 12;
            line-height: 22px;
            color: rgba(0, 0, 0, 0.7);
        }

        /* -- Items Table -- */

        .items-table {
            padding: 30px 30px 10px 30px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
            margin: 0 30px;
        }

        .item-table-heading {
            font-size: 13.5;
            text-align: center;
            color: rgba(0, 0, 0, 0.85);
            padding: 5px;
        }

        .item-table-heading-row td {
            padding: 5px;
            padding-bottom: 10px;
        }

        .item-table-heading-row {
            border-bottom: 1px solid red;
        }

        tr.item-table-heading-row th {
            border-bottom: 0.620315px solid #E8E8E8;
            font-size: 12px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 12px;
            line-height: 18px;
        }

        .item-cell {
            font-size: 13;
            color: #040405;
            text-align: center;
            padding: 5px;
            padding-top: 10px;
        }

        .item-description {
            color: #595959;
            font-size: 9px;
            line-height: 12px;
            page-break-inside: avoid;
        }

        /* -- Total Display Table -- */


        .total-display-container {
            padding: 0 25px;
        }

        .item-cell-table-hr {
            margin: 0 25px 0 30px;
        }

        .total-display-table {
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-top: 20px;
            float: right;
            width: auto;
        }

        .total-table-attribute-label {
            font-size: 12px;
            color: #55547A;
            text-align: left;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 12px;
            color: #040405;
            padding-right: 10px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .total-border-left {
            border: 1px solid #E8E8E8 !important;
            border-right: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .total-border-right {
            border: 1px solid #E8E8E8 !important;
            border-left: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        /* -- Notes -- */

        .notes {
            font-size: 12px;
            color: #595959;
            margin-top: 80px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            white-space: nowrap;
            height: 19.87px;
            padding-bottom: 10px;
        }

        /* -- Helpers -- */

        .text-primary {
            color: #5851DB;
        }

        .text-center {
            text-align: center
        }

        table .text-left {
            text-align: left;
        }

        table .text-right {
            text-align: right;
        }

        .border-0 {
            border: none;
        }

        .py-2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .py-3 {
            padding: 3px 0;
        }

        .pr-20 {
            padding-right: 20px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pl-20 {
            padding-left: 20px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        .pl-0 {
            padding-left: 0;
        }

    </style>

    @if (App::isLocale('th'))
        @include('app.pdf.locale.th')
    @endif
</head>

<body>
    <div class="header-container">
        <table width="100%">
            <tr>
                @if ($logo)
                    <td width="60%" class="header-section-left">
                        <img class="header-logo" style="height: 50px;" src="{{ $logo }}" alt="Company Logo">
                    </td>
                @else
                    <td width="60%" class="header-section-left" style="padding-top: 0px;">
                        @if ($estimate->customer->company)
                            <h1 class="header-logo"> {{ $estimate->customer->company->name }} </h1>
                        @endif
                    </td>
                @endif
                <td width="40%" class="header-section-right estimate-details-container">
                    <h1>@lang('pdf_estimate_label')</h1>
                    <h4>{{ $estimate->estimate_number }}</h4>
                    <h4>{{ $estimate->formattedEstimateDate }}</h4>
                </td>
            </tr>
        </table>
    </div>
    <hr>
    <div class="content-wrapper">
        <div class="address-container">
            <div class="company-address-container company-address">
                {!! $company_address !!}
            </div>

            @if ($shipping_address !== '</br>')
                <div class="shipping-address-container shipping-address">
                    @if ($shipping_address)
                        <b>@lang('pdf_ship_to')</b> <br>
                        {!! $shipping_address !!}
                    @endif
                </div>
            @endif

            <div class="billing-address-container billing-address" @if ($shipping_address === '</br>') style="float:right; margin-right:30px;" @endif>
                @if ($billing_address)
                    <b>@lang('pdf_bill_to')</b> <br>
                    {!! $billing_address !!}
                @endif
            </div>
            <div style="clear: both;"></div>
        </div>

        @include('app.pdf.estimate.partials.table')

        <div class="notes">
            @if ($notes)
                <div class="notes-label">
                    @lang('pdf_notes')
                </div>
                {!! $notes !!}
            @endif
        </div>
    </div>
</body>

</html>
