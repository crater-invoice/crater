<?php

namespace Crater\Http\Controllers\V1\Dashboard;

use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Illuminate\Http\Request;

class DashboardChartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $expensesCategories = Expense::with('category')
            ->whereCompany($request->header('company'))
            ->expensesAttributes()
            ->get();

        $amounts = $expensesCategories->pluck('total_amount');
        $names = $expensesCategories->pluck('category.name');

        return response()->json([
            'amounts' => $amounts,
            'categories' => $names,
        ]);
    }
}
