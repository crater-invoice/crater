<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Http\Requests;
use Crater\User;
use Crater\Currency;
use Crater\Setting;
use Crater\Item;
use Crater\PaymentMethod;
use Crater\Unit;
use Crater\TaxType;
use DB;
use Carbon\Carbon;
use Auth;
use Crater\Company;
use Crater\CompanySetting;

class UsersController extends Controller
{
    public function getBootstrap(Request $request)
    {
        $user = Auth::user();

        $company = $request->header('company') ?? 1;

        $customers = User::with('billingAddress', 'shippingAddress')
            ->customer()
            ->whereCompany($company)
            ->latest()
            ->get();

        $currencies = Currency::latest()->get();

        $default_language = CompanySetting::getSetting('language', $company);

        $default_currency = Currency::findOrFail(
            CompanySetting::getSetting('currency', $company)
        );

        $moment_date_format = CompanySetting::getSetting(
            'moment_date_format',
            $request->header('company')
        );

        $fiscal_year = CompanySetting::getSetting(
            'fiscal_year',
            $request->header('company')
        );

        $items = Item::with('taxes')->get();

        $taxTypes = TaxType::latest()->get();

        $paymentMethods = PaymentMethod::whereCompany($request->header('company'))
            ->latest()
            ->get();

        $units = Unit::whereCompany($request->header('company'))
            ->latest()
            ->get();

        return response()->json([
            'user' => $user,
            'customers' => $customers,
            'currencies' => $currencies,
            'default_currency' => $default_currency,
            'default_language' => $default_language,
            'company' => $user->company,
            'companies' => Company::all(),
            'items' => $items,
            'taxTypes' => $taxTypes,
            'moment_date_format' => $moment_date_format,
            'paymentMethods' => $paymentMethods,
            'units' => $units,
            'fiscal_year' => $fiscal_year,
        ]);
    }

    public function ping()
    {
        return response()->json([
            'success' => 'crater-self-hosted'
        ]);
    }
}
