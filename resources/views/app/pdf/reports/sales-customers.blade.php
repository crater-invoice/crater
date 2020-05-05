<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Customer Report</title>
    {{-- <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet"> --}}
    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
        }

        .main-container {
            /* padding: 30px 80px; */
        }

        .sub-container{
            padding: 0px 20px;
        }

        .header {
            width: 100%;
        }

        table {
            border-collapse: collapse;
        }

        .heading-text {
            font-weight: 600;
            font-size: 24px;
            color: #5851D8;
            width: 100%;
            text-align: left;
            padding: 0px;
            margin: 0px;
        }

        .heading-date-range {
            font-weight: 600;
            font-size: 15px;
            color: #A5ACC1;
            width: 100%;
            text-align: right;
            padding: 0px;
            margin: 0px;
        }

        .sub-heading-text {
            font-weight: 600;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 30px;
        }

        .sales-cusutomer-name {
            margin-top: 20px;
            padding-left: 3px;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .sales-table-container {
            padding-left: 10px;
        }

        .sales-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .sales-information-text {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .sales-money {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .sales-total-indicator-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .sales-total-container {
            padding-top: 10px;
        }

        .sales-total-text {
            padding-top: 10px;
            padding-right: 30px;
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: 500;
            font-size: 16px;
            line-height: 21px;
            text-align: right;
            color: #040405;
        }

        .profit-indicator-table {
            width: 100%;
            margin-top: 40px;
            padding: 15px 20px;
            background: #F9FBFF;
            box-sizing: border-box;
        }

        .profit-title {
            padding: 0px;
            margin: 0px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .profit-amount {
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: 500;
            font-size: 20px;
            line-height: 21px;
            color: #5851D8;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sub-container">
            <table class="header">
                <tr>
                    <td>
                        <p class="heading-text">{{ $company->name }}</p>
                    </td>
                    <td>
                        <p class="heading-date-range">{{ $from_date }} - {{ $to_date }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="sub-heading-text text-center">Sales Report: By Customer</p>
                    </td>
                </tr>
            </table>

            @foreach ($customers as $customer)
                <p class="sales-cusutomer-name">{{ $customer->name }}</p>
                <div class="sales-table-container">
                    <table class="sales-table">
                        @foreach ($customer->invoices as $invoice)
                            <tr>
                                <td>
                                    <p class="sales-information-text">
                                        {{ $invoice->formattedInvoiceDate }} ({{ $invoice->invoice_number }})
                                    </p>
                                </td>
                                <td>
                                    <p class="sales-money">
                                        {!! format_money_pdf($invoice->total) !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <table class="sales-total-indicator-table">
                    <tr>
                        <td class="sales-total-container">
                            <p class="sales-total-text">
                                {!! format_money_pdf($customer->totalAmount) !!}
                            </p>
                        </td>
                    </tr>
                </table>
            @endforeach
        </div>


        <table class="profit-indicator-table">
            <tr>
                <td>
                    <p class="profit-title">TOTAL SALES</p>
                </td>
                <td>
                    <p class="profit-amount">
                        {!! format_money_pdf($totalAmount) !!}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
