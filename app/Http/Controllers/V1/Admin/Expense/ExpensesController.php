<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Expense;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\DeleteExpensesRequest;
use InvoiceShelf\Http\Requests\ExpenseRequest;
use InvoiceShelf\Http\Resources\ExpenseResource;
use InvoiceShelf\Models\Expense;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $expenses = Expense::with('category', 'creator', 'fields')
            ->whereCompany()
            ->leftJoin('customers', 'customers.id', '=', 'expenses.customer_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->applyFilters($request->all())
            ->select('expenses.*', 'expense_categories.name', 'customers.name as user_name')
            ->paginateData($limit);

        return ExpenseResource::collection($expenses)
            ->additional(['meta' => [
                'expense_total_count' => Expense::whereCompany()->count(),
            ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenseRequest $request)
    {
        $this->authorize('create', Expense::class);

        $expense = Expense::createExpense($request);

        return new ExpenseResource($expense);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);

        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $expense->updateExpense($request);

        return new ExpenseResource($expense);
    }

    public function delete(DeleteExpensesRequest $request)
    {
        $this->authorize('delete multiple expenses');

        Expense::destroy($request->ids);

        return response()->json([
            'success' => true,
        ]);
    }
}
