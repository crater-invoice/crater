<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tax Summary Report</title>
    {{-- <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet"> --}}
    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
        }

        /* html {
            margin: 0px;
            padding: 0px;
        } */

        table {
            border-collapse: collapse;
        }

        .main-container {
            /* padding: 30px 60px; */
        }

        .sub-container{
            padding: 0px 20px;
        }

        .header {
            width: 100%;
            margin-bottom: 60px
        }

        .heading-text {
            font-style: normal;
            font-weight: 600;
            font-size: 24px;
            color: #5851D8;
            width: 100%;
            text-align: left;
            padding: 0px;
            margin: 0px;
        }

        .heading-date-range {
            font-style: normal;
            font-weight: 600;
            font-size: 15px;
            color: #A5ACC1;
            width: 100%;
            text-align: right;
            padding: 0px;
            margin: 0px;
        }

        .sub-heading-text {
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            /* line-height: 21px; */
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 6px;
        }

        .types-title {
            margin-top: 20px;
            padding-left: 3px;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .tax-title {
            margin-top: 60px;
            padding-left: 3px;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .tax-table-container {
            padding-left: 10px;
        }

        .tax-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .tax-title {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .tax-money {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .tax-total-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .tax-total-cell {
            padding-right: 20px;
            padding-top: 10px;
        }

        .tax-total {
            padding-top: 10px;
            padding-right: 30px;
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-style: normal;
            font-weight: 500;
            font-size: 16px;
            line-height: 21px;
            text-align: right;
            color: #040405;
        }

        .total-tax-table {
            width: 100%;
            margin-top: 40px;
            padding: 15px 20px;
            background: #F9FBFF;
            box-sizing: border-box;
        }

        .total-tax-title {
            padding: 0px;
            margin: 0px;
            text-align: left;
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .total-tax-money {
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-style: normal;
            font-weight: 500;
            font-size: 20px;
            line-height: 21px;
            color: #5851D8;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sub-container">
            <table class="header">
                <tr>
                    <td>
                        <p class="heading-text">
                            {{ $company->name }}
                        </p>
                    </td>
                    <td>
                        <p class="heading-date-range">
                            {{ $from_date }} - {{ $to_date }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="sub-heading-text">TAX REPORT</p>
                    </td>
                </tr>
            </table>
            <p class="types-title">Tax Types</p>
            <div class="tax-table-container">
                <table class="tax-table">
                    @foreach ($taxTypes as $tax)
                        <tr>
                            <td>
                                <p class="tax-title">
                                    {{ $tax->taxType->name }}
                                </p>
                            </td>
                            <td>
                                <p class="tax-money">
                                    {!! format_money_pdf($tax->total_tax_amount) !!}
                                </p>
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
        </div>

        <table class="tax-total-table">
            <tr>
                <td class="tax-total-cell">
                    <p class="tax-total">
                        {!! format_money_pdf($totalTaxAmount) !!}
                    </p>
                </td>
            </tr>
        </table>
        <table class="total-tax-table">
            <tr>
                <td>
                    <p class="total-tax-title">TOTAL TAX</p>
                </td>
                <td>
                    <p class="total-tax-money">
                        {!! format_money_pdf($totalTaxAmount) !!}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
