<?php

namespace Crater\Http\Controllers\V1\Dashboard;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $invoiceTotals = [];
        $expenseTotals = [];
        $receiptTotals = [];
        $netProfits = [];
        $i = 0;
        $months = [];
        $monthCounter = 0;
        $fiscalYear = CompanySetting::getSetting('fiscal_year', $request->header('company'));
        $startDate = Carbon::now();
        $start = Carbon::now();
        $end = Carbon::now();
        $terms = explode('-', $fiscalYear);

        if ($terms[0] <= $start->month) {
            $startDate->month($terms[0])->startOfMonth();
            $start->month($terms[0])->startOfMonth();
            $end->month($terms[0])->endOfMonth();
        } else {
            $startDate->subYear()->month($terms[0])->startOfMonth();
            $start->subYear()->month($terms[0])->startOfMonth();
            $end->subYear()->month($terms[0])->endOfMonth();
        }

        if ($request->has('previous_year')) {
            $startDate->subYear()->startOfMonth();
            $start->subYear()->startOfMonth();
            $end->subYear()->endOfMonth();
        }

        while ($monthCounter < 12) {
            array_push(
                $invoiceTotals,
                Invoice::whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->where('status', '!=', Invoice::STATUS_DRAFT)
                ->whereCompany($request->header('company'))
                ->sum('total')
            );
            array_push(
                $expenseTotals,
                Expense::whereBetween(
                    'expense_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->whereCompany($request->header('company'))
                ->sum('amount')
            );
            array_push(
                $receiptTotals,
                Payment::whereBetween(
                    'payment_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                ->whereCompany($request->header('company'))
                ->sum('amount')
            );
            array_push(
                $netProfits,
                ($receiptTotals[$i] - $expenseTotals[$i])
            );
            $i++;
            array_push($months, $start->format('M'));
            $monthCounter++;
            $end->startOfMonth();
            $start->addMonth()->startOfMonth();
            $end->addMonth()->endOfMonth();
        }

        $start->subMonth()->endOfMonth();

        $salesTotal = Invoice::whereCompany($request->header('company'))
            ->where('status', '!=', Invoice::STATUS_DRAFT)
            ->whereBetween(
                'invoice_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('total');
        $totalReceipts = Payment::whereCompany($request->header('company'))
            ->whereBetween(
                'payment_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('amount');
        $totalExpenses = Expense::whereCompany($request->header('company'))
            ->whereBetween(
                'expense_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->sum('amount');
        $netProfit = (int)$totalReceipts - (int)$totalExpenses;

        $chartData = [
            'months' => $months,
            'invoiceTotals' => $invoiceTotals,
            'expenseTotals' => $expenseTotals,
            'receiptTotals' => $receiptTotals,
            'netProfits' => $netProfits,
        ];

        $customersCount = User::customer()->whereCompany($request->header('company'))->count();
        $invoicesCount = Invoice::whereCompany($request->header('company'))
            ->where('status', '!=', Invoice::STATUS_DRAFT)
            ->count();
        $estimatesCount = Estimate::whereCompany($request->header('company'))->count();
        $totalDueAmount = Invoice::whereCompany($request->header('company'))
            ->where('status', '!=', Invoice::STATUS_DRAFT)
            ->sum('due_amount');
        $dueInvoices = Invoice::with('user')
            ->whereCompany($request->header('company'))
            ->where('status', '!=', Invoice::STATUS_DRAFT)
            ->where('due_amount', '>', 0)
            ->take(5)
            ->latest()
            ->get();
        $estimates = Estimate::with('user')->whereCompany($request->header('company'))->take(5)->latest()->get();

        return response()->json([
            'dueInvoices' => $dueInvoices,
            'estimates' => $estimates,
            'estimatesCount' => $estimatesCount,
            'totalDueAmount' => $totalDueAmount,
            'invoicesCount' => $invoicesCount,
            'customersCount' => $customersCount,
            'chartData' => $chartData,
            'salesTotal' => $salesTotal,
            'totalReceipts' => $totalReceipts,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
        ]);
    }
}
