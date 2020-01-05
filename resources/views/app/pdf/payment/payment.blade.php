<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    {{-- <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
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
            top: 80px;
            left: 0px;
            right: -70px;
            width: 100%;
        }

        .header-left {
            padding-top: 45px;
            padding-bottom: 45px;
            padding-left: 30px;
            display:inline-block;
            width:30%;
        }
        .header-table {
            position: absolute;
            width: 100%;
            height: 150px;
            left: 0px;
            top: -60px;
        }
        .header-logo {
            position: absolute;
            height: 50px;
            text-transform: capitalize;
            color: #817AE3;
        }
        .header-right {
            display:inline-block;
            position: absolute;
            right:0;
            padding: 15px 30px 15px 0px;
            float: right;
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
            font-size: 20px;
            color: rgba(0, 0, 0, 0.7);
        }

        .TextColor1 {
            font-size: 16px;
            color: rgba(0, 0, 0, 0.5);
        }

        @page {
            margin-top: 60px !important;
        }
        .wrapper {
           display: block;
           height: 200px;
        }

        .address {
            display: inline-block;
            padding-top: 20px
        }

        .bill-add {
            display: block;
            float:left;
            width:40%;
            padding: 0 0 0 30px;
        }
        .company {
            padding-left: 30px;
            display: inline;
            float:left;
            width:30%;
        }

        .company h1 {
            font-style: normal;
            font-weight: bold;
            font-size: 18px;
            line-height: 22px;
            letter-spacing: 0.05em;
        }

        .company-add {
            text-align: left;
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin: 0px;
        }

         /* -------------------------- */
        /* shipping style */
        .ship-to {
            padding-top: 5px;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            margin-bottom: 0px;
        }

        .ship-user-name {
            padding: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            margin: 0px;
        }

        .ship-user-address {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin: 0px;
            width: 160px;
        }

        .ship-user-phone {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin: 0px;
        }

         /* -------------------------- */
        /* billing style */
        .bill-to {
            padding-top: 5px;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            margin-bottom: 0px;
            color: #55547A;
        }

        .bill-user-name {
            padding: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            margin: 0px;
        }

        .bill-user-address {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin:0px;
            width: 160px;
        }

        .bill-user-phone {
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin: 0px;

        }


        .job-add {
            display: inline;
            position: absolute;
            float: right;
            width: 40%;
            height: 120px;
            padding: 20px 30px 0 0;
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
        }

        .textStyle2 {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
            text-align: right;
        }
        .main-table-header td {
            padding: 10px;
        }
        .main-table-header {
            border-bottom: 1px solid red;
        }
        tr.main-table-header th {
            font-style: normal;
            font-weight: 600;
            font-size: 12px;
            line-height: 18px;
        }
        tr.item-details td {
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 18px;
        }
        .table2 {
            margin-top: 188px;
            border-bottom: 1px solid #EAF1FB;
            padding: 0px 30px 0 30px;
            page-break-before: avoid;
            page-break-after: auto;
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

        .items {
            font-size: 13;
            color: rgba(0, 0, 0, 0.6);
            text-align: center;
            padding: 5px;
        }

        .note-header {
            font-size: 13;
            color: rgba(0, 0, 0, 0.6);
        }

        .note-text {
            font-size: 10;
            color: rgba(0, 0, 0, 0.6);
        }

        .padd8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .padd2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .table3 {
            border: 1px solid #EAF1FB;
            border-top: none;
            box-sizing: border-box;
            width: 630px;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
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

        td.invoice-total1 {
            text-align:left;
            padding: 15px 0 15px 10px;
            font-size:12px;
            line-height: 18px;
            color: #55547A;
            border-bottom:1px solid #E8E8E8;
            border-top:1px solid #E8E8E8;
            border-left:1px solid #E8E8E8;
        }

        td.invoice-total2 {
            font-weight: 500;
            text-align: right;
            font-size:12px;
            line-height: 18px;
            padding: 15px 10px 15px 0;
            color: #5851DB;
            border-bottom:1px solid #E8E8E8;
            border-top:1px solid #E8E8E8;
            border-right:1px solid #E8E8E8;
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
        .company-details h1 {
            margin:0;
            font-style: normal;
            font-weight: bold;
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            text-align: left;
            max-width: 220px;
        }
        .company-details h4 {
            margin:0;
            font-style: normal;
            font-weight: 100;
            font-size: 18px;
            line-height: 25px;
            text-align: right;
        }
        .company-details h3 {
             margin-bottom:1px;
             margin-top:0;
        }
        tr.total td {
            border-bottom:1px solid #E8E8E8;
            border-top:1px solid #E8E8E8;
        }

        .notes {
            font-style: normal;
            font-weight: 300;
            font-size: 12px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            height: 19.87px;
            padding-bottom: 10px;
        }

        .content-header {
            margin-top: 120px;
            width: 100%;
            text-align: center;
        }

        p {
            padding: 0 0 0 0;
            margin:  0 0 0 0;
        }

        .content-header span {
            font-weight: 500;
            font-size: 14px;
            line-height: 25px;
            border-bottom: 1px solid #B9C1D1;
        }

        .total-amount {
            width: 315px;
            display: block;
            margin-right: 30px;
            background: #F9FBFF;
            border: 1px solid #EAF1FB;
            box-sizing: border-box;
            float: right;
            padding: 12px 15px 15px 15px;
        }

        .total-amount-label {
            display: inline;
            font-weight: 600;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .total-amount span {
            float: right;
            font-weight: 500;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #5851D8;
        }

    </style>
</head>
<body>
    <div class="header-table">
        <table width="100%">
            <tr>
                @if($logo)
                    <td class="header-left">
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                @else
                    @if($payment->user->company)
                    <td class="header-left" style="padding-top:0px;">
                        <h1 class="header-logo"> {{$payment->user->company->name}} </h1>
                    @endif
                @endif
                </td>
                <td class="header-right company-details">
                    @include('app.pdf.payment.partials.company-address')
                </td>
            </tr>
        </table>
    </div>
    <hr style="border: 0.620315px solid #E8E8E8;">
    <p class="content-header">
        <span>PAYMENT RECEIPT</span>
    </p>
    <div class="wrapper">
        <div class="address">
            <div class="bill-add">
                <div style="float:left;">
                    @include('app.pdf.payment.partials.billing-address')
                </div>
                <div style="float:right;">
                </div>
                <div style="clear: both;"></div>
            </div>

            <div class="job-add">
                <table width="100%">
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Payment Date</td>
                        <td class="textStyle2"> &nbsp;{{$payment->formattedPaymentDate}}</td>
                    </tr>
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Payment Number</td>
                        <td class="textStyle2"> &nbsp;{{$payment->payment_number}}</td>
                    </tr>
                    <tr>
                        <td class="textStyle1" style="text-align: left; color: #55547A">Payment Mode</td>
                        <td class="textStyle2"> &nbsp;{{$payment->paymentMethod->name}}</td>
                    </tr>
                    @if ($payment->invoice && $payment->invoice->invoice_number)
                        <tr>
                            <td class="textStyle1" style="text-align: left; color: #55547A">Invoice</td>
                            <td class="textStyle2"> &nbsp;{{$payment->invoice->invoice_number}}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="total-amount">
        <p class="total-amount-label">Amount Received</p>
        <span>{!! format_money_pdf($payment->amount, $payment->user->currency) !!}</span>
    </div>
</body>
</html>
