<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Item Report</title>
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
            /* padding: 30px 80px; */
        }

        .sub-container{
            padding: 0px 20px;
        }

        .header {
            width: 100%;
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
            line-height: 21px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 30px;
        }

        .income-table {
            margin-top: 53px;
            width: 100%;
        }

        .income-title {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
            text-align: left;
        }

        .income-money {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: 500;
            font-size: 16px;
            line-height: 21px;
            text-align: right;
            color: #040405;
            text-align: right;
        }

        .expenses-title {
            margin-top: 20px;
            padding-left: 3px;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .expenses-table-container {
            padding-left: 10px;
        }

        .expenses-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .expense-title {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .expense-money {
            padding: 0px;
            margin: 0px;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .expense-total-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .expense-total-cell {
            padding-top: 10px;
        }

        .expense-total {
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

        .profit-table {
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
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .profit-money {
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-style: normal;
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
                        <p class="sub-heading-text text-center">Sales Report: By Item</p>
                    </td>
                </tr>
            </table>

            {{-- <table class="income-table">
                <tr>
                    <td>
                        <p class="income-title">Income</p>
                    </td>
                    <td>
                        <p class="income-money">{{ $income }}</p>
                    </td>
                </tr>
            </table> --}}
            <p class="expenses-title">Items</p>
            @foreach ($items as $item)
                <div class="expenses-table-container">
                    <table class="expenses-table">
                        <tr>
                            <td>
                                <p class="expense-title">
                                    {{ $item->name }}
                                </p>
                            </td>
                            <td>
                                <p class="expense-money">
                                    {!! format_money_pdf($item->total_amount) !!}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach

                <table class="expense-total-table">
                    <tr>
                        <td class="expense-total-cell">
                            <p class="expense-total">
                                {!! format_money_pdf($totalAmount) !!}
                            </p>
                        </td>
                    </tr>
                </table>
        </div>


        <table class="profit-table">
            <tr>
                <td>
                    <p class="profit-title">TOTAL SALES</p>
                </td>
                <td>
                    <p class="profit-money">
                        {!! format_money_pdf($totalAmount) !!}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
