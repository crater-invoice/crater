<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Customer;
use InvoiceShelf\Models\User;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $customers = Customer::applyFilters($request->only(['search']))
            ->whereCompany()
            ->latest()
            ->paginate(10);

        if ($user->isOwner()) {
            $users = User::applyFilters($request->only(['search']))
                ->latest()
                ->paginate(10);
        }

        return response()->json([
            'customers' => $customers,
            'users' => $users ?? [],
        ]);
    }
}
