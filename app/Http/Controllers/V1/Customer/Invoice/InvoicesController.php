<?php

namespace Crater\Http\Controllers\V1\Customer\Invoice;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\Customer\InvoiceResource;
use Crater\Models\Company;
use Crater\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $invoices = Invoice::with(['items', 'customer', 'creator', 'taxes'])
            ->where('status', '<>', 'DRAFT')
            ->applyFilters($request->all())
            ->whereCustomer(Auth::guard('customer')->id())
            ->latest()
            ->paginateData($limit);

        return (InvoiceResource::collection($invoices))
            ->additional(['meta' => [
                'invoiceTotalCount' => Invoice::where('status', '<>', 'DRAFT')->whereCustomer(Auth::guard('customer')->id())->count(),
            ]]);
    }

    public function show(Company $company, $id)
    {
        $invoice = $company->invoices()
            ->whereCustomer(Auth::guard('customer')->id())
            ->where('id', $id)
            ->first();

        if (! $invoice) {
            return response()->json(['error' => 'invoice_not_found'], 404);
        }

        return new InvoiceResource($invoice);
    }
}
