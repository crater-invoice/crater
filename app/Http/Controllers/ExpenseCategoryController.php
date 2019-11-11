<?php
namespace Laraspace\Http\Controllers;

use Laraspace\ExpenseCategory;
use Laraspace\Expense;
use Laraspace\User;
use Illuminate\Http\Request;
use Laraspace\Http\Requests\ExpenseCategoryRequest;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = ExpenseCategory::whereCompany($request->header('company'))->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('app.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseCategoryRequest $request)
    {
        $category = new ExpenseCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->company_id = $request->header('company');
        $category->save();

        return response()->json([
            'category' => $category,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Laraspace\ExpenseCategory $ExpenseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $ExpenseCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Laraspace\ExpensesCategory $ExpensesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Laraspace\ExpenseCategory $ExpenseCategory
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseCategoryRequest $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return response()->json([
            'category' => $category,
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Laraspace\ExpensesCategory $expensesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = ExpenseCategory::find($id);
        if ($category->expenses() && $category->expenses()->count() > 0) {
            return response()->json([
                'success' => false
            ]);
        }
        $category->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
