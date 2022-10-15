<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('pdf_expense_report_label')</title>
    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
        }

        table {
            border-collapse: collapse;
        }

        .sub-container {
            padding: 0px 20px;
        }

        .report-header {
            width: 100%;
        }

        .heading-text {
            font-weight: bold;
            font-size: 24px;
            color: #5851D8;
            width: 100%;
            text-align: left;
            padding: 0px;
            margin: 0px;
        }

        .heading-date-range {
            font-weight: normal;
            font-size: 15px;
            color: #A5ACC1;
            width: 100%;
            text-align: right;
            padding: 0px;
            margin: 0px;
        }

        .sub-heading-text {
            font-weight: normal;
            font-size: 16px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 6px;
        }

        .expenses-title {
            margin-top: 60px;
            padding-left: 3px;
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
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .expense-amount {
            padding: 0px;
            margin: 0px;
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
            padding-right: 20px;
            padding-top: 10px;
        }

        .expense-total {
            padding-top: 10px;
            padding-right: 30px;
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            text-align: right;
            color: #040405;
        }

        .report-footer {
            width: 100%;
            margin-top: 40px;
            padding: 15px 20px;
            background: #F9FBFF;
            box-sizing: border-box;
        }

        .report-footer-label {
            padding: 0px;
            margin: 0px;
            text-align: left;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .report-footer-value {
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: bold;
            font-size: 20px;
            line-height: 21px;
            color: #5851D8;
        }
    </style>

    @if (App::isLocale('th'))
    @include('app.pdf.locale.th')
    @endif
</head>

<body>
    <div class="sub-container">
        <table class="report-header">
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
                    <p class="sub-heading-text">@lang('pdf_expense_report_label')</p>
                </td>
            </tr>
        </table>
        <p class="expenses-title">@lang('pdf_expenses_label')</p>
        <div class="expenses-table-container">
            <table class="expenses-table">
                @foreach ($expenseCategories as $expenseCategory)
                <tr>
                    <td>
                        <p class="expense-title">
                            {{ $expenseCategory->category->name }}
                        </p>
                    </td>
                    <td>
                        <p class="expense-amount">
                            {!! format_money_pdf($expenseCategory->total_amount, $currency) !!}
                        </p>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <table class="expense-total-table">
        <tr>
            <td class="expense-total-cell">
                <p class="expense-total">{!! format_money_pdf($totalExpense, $currency) !!}</p>
            </td>
        </tr>
    </table>
    <table class="report-footer">
        <tr>
            <td>
                <p class="report-footer-label">@lang('pdf_total_expenses_label')</p>
            </td>
            <td>
                <p class="report-footer-value">{!! format_money_pdf($totalExpense, $currency) !!}</p>
            </td>
        </tr>
    </table>
</body>

</html>