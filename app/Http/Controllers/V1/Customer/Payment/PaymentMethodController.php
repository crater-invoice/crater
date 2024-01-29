<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer\Payment;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\Customer\PaymentMethodResource;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Company $company)
    {
        return PaymentMethodResource::collection(PaymentMethod::where('company_id', $company->id)->get());
    }
}
