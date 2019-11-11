<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    {{-- <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet"> --}}
    <style type="text/css">
        body {
            font-family: 'Roboto', sans-serif;
        }

        html {
            margin: 0px;
            padding: 0px;
        }

        table {
            border-collapse: collapse;
        }

        hr {
            color:rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 90px;
            left: 0px;
            width: 100%;
            margin: 0 30px 0 30px;
        }

        .header-center {
            text-align: center
        }

        .header-table {
            position: absolute;
            width: 100%;
            height: 90px;
            left: 0px;
            top: 0px;
        }

        .header-logo {
            height: 50px;
            margin-top: 20px;
            text-transform: capitalize;
            color: #817AE3;
        }

        .inv-flex{
            display:flex;
        }

        .inv-data{
            text-align:right;
            margin-right:120px;
        }
        .inv-value{
            text-align:left;
            margin-left:160px;
        }
        .header {
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            color: rgba(0, 0, 0, 0.7);
        }

        .TextColor1 {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: rgba(0, 0, 0, 0.5);
        }

        .wrapper {
           display: block;
           padding-top: 60px;
        }

        .address {
            /* display: inline-block; */
            padding-top: 30px
        }

        .company {
            float: left;
            padding-left: 30px;
            font-weight: normal;
            display: inline;
            float:left;
            width:30%;
            text-transform: capitalize;
            margin-bottom: 2px;
        }

        .company h1 {
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
        }

        .company-add {
            margin-top: 2px;
            text-align: left;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 15px;
            color: #595959;
        }

        .job-add {
            /* display: inline; */
            float: right;
            padding: 10px 30px 0 0;
        }
        .amount-due {
            background-color: #f2f2f2;
        }

        .textRight {
            text-align: right;
        }

        .textLeft {
            text-align: left;
        }

        .textStyle1 {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            padding-right: 40px;
        }

        .textStyle2 {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            text-align: right;
        }
        .bill-add {
            width:45%;
            padding: 0px 0 0 0px;
        }

        /* -------------------------- */
        /* shipping style */

        .ship-address-container {
            float: right;
            padding-left: 30px;
        }

        .ship-to {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .ship-user-name {
            max-width: 250px
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }
        .ship-user-address {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
        }
        .ship-user-phone {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
        }

        /* -------------------------- */
        /* billing style */

        .bill-address-container {
            float: left;
            padding-left: 30px;
        }

        .bill-to {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .bill-user-name {
            max-width: 250px
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }

        .bill-user-address {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
        }

        .bill-user-phone {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
        }

        .table2 {
            margin-top: 35px;
            border-bottom: 1px solid #EAF1FB;
            padding: 0px 30px 0 30px;
        }

        .table2 hr {
            height:0.1px;
        }

        .ItemTableHeader {
            font-size: 13.5;
            text-align: center;
            color: rgba(0, 0, 0, 0.85);
            padding: 5px;
        }

        tr.main-table-header td {
            border-bottom: 1px solid #EAF1FB;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
        }

        tr.item-details td {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
        }

        .items {
            font-size: 13;
            color: rgba(0, 0, 0, 0.6);
            text-align: center;
            padding: 5px;
        }

        .padd8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .table3 {
            border: 1px solid #EAF1FB;
            border-top: none;
            padding-right: 30px;
            box-sizing: border-box;
            width: 230px;
            height: 100px;
            position: absolute;
            right: -25;
        }

        .text-per-item-table3 {
            border: 1px solid #EAF1FB;
            border-top: none;
            padding-right: 30px;
            box-sizing: border-box;
            width: 260px;
            /* height: 100px; */
            position: absolute;
            right: -25;
        }

        .inv-item {
            border-color: #d9d9d9;
        }

        .no-border {
            border: none;
        }

        .desc {
            font-weight: 100;
            text-align: justify;
            font-size: 10px;
            margin-bottom: 15px;
            margin-top:7px;
            color:rgba(0, 0, 0, 0.85);
        }


    </style>
</head>
<body>
    <div class="header-table">
        <table width="100%">
            <tr>
                <td class="header-center">
                    @if($logo)
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                        @if($invoice->user->company)
                            <h2 class="header-logo"> {{$invoice->user->company->name}} </h2>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <hr class="header-line" />
    <div class="wrapper">
        <div class="address">
            <div class="company">
                @include('app.pdf.invoice.partials.company-address')
            </div>
            <div class="job-add">
                <table>
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Invoice Number</td>
                        <td class="textStyle2"> &nbsp;{{$invoice->invoice_number}}</td>
                    </tr>
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Invoice Date </td>
                        <td class="textStyle2"> &nbsp;{{$invoice->formattedInvoiceDate}}</td>
                    </tr>
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Due date</td>
                        <td class="textStyle2"> &nbsp;{{$invoice->formattedDueDate}}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="bill-add">
            <div class="bill-address-container">
                    @include('app.pdf.invoice.partials.billing-address')

            </div>
            @if($invoice->user->billingaddress->name || $invoice->user->billingaddress->address_street_1 || $invoice->user->billingaddress->address_street_2 || $invoice->user->billingaddress->country || $invoice->user->billingaddress->state || $invoice->user->billingaddress->city || $invoice->user->billingaddress->zip || $invoice->user->billingaddress->phone)
                <div class="ship-address-container">
            @else
                <div class="ship-address-container " style="float:left;padding-left:0px;">
            @endif
                @include('app.pdf.invoice.partials.shipping-address')
            </div>
            <div style="clear: both;"></div>
        </div>
        @include('app.pdf.invoice.partials.table')
    </div>
</body>
</html>
