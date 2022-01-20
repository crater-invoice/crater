<?php

namespace Crater\Http\Controllers\V1\Customer\General;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\Customer\CustomerResource;
use Crater\Models\Currency;
use Crater\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BootstrapController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        foreach (\Menu::get('customer_portal_menu')->items->toArray() as $data) {
            if ($customer) {
                $menu[] = [
                    'title' => $data->title,
                    'link' => $data->link->path['url'],
                ];
            }
        }

        return (new CustomerResource($customer))
            ->additional(['meta' => [
                'menu' => $menu,
                'current_customer_currency' => Currency::find($customer->currency_id),
                'modules' => Module::where('enabled', true)->pluck('name'),
            ]]);
    }
}
