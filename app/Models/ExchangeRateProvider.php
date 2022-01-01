<?php

namespace Crater\Models;

use Crater\Http\Requests\ExchangeRateProviderRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ExchangeRateProvider extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'currencies' => 'array',
        'driver_config' => 'array',
        'active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setCurrenciesAttribute($value)
    {
        $this->attributes['currencies'] = json_encode($value);
    }

    public function setDriverConfigAttribute($value)
    {
        $this->attributes['driver_config'] = json_encode($value);
    }

    public function scopeWhereCompany($query)
    {
        $query->where('exchange_rate_providers.company_id', request()->header('company'));
    }

    public static function createFromRequest(ExchangeRateProviderRequest $request)
    {
        $exchangeRateProvider = self::create($request->getExchangeRateProviderPayload());

        return $exchangeRateProvider;
    }

    public function updateFromRequest(ExchangeRateProviderRequest $request)
    {
        $this->update($request->getExchangeRateProviderPayload());

        return $this;
    }

    public static function checkActiveCurrencies($request)
    {
        $query = ExchangeRateProvider::whereJsonContains('currencies', $request->currencies)
            ->where('active', true)
            ->get();

        return $query;
    }

    public function checkUpdateActiveCurrencies($request)
    {
        $query = ExchangeRateProvider::where('active', $request->active)
            ->where('id', '<>', $this->id)
            ->whereJsonContains('currencies', $request->currencies)
            ->get();

        return $query;
    }

    public static function checkExchangeRateProviderStatus($request)
    {
        switch ($request['driver']) {
            case 'currency_freak':
                $url = "https://api.currencyfreaks.com/latest?apikey=".$request['key']."&symbols=INR&base=USD";
                $response = Http::get($url)->json();

                if (array_key_exists('success', $response)) {
                    if ($response["success"] == false) {
                        return respondJson($response["error"]["message"], $response["error"]["message"]);
                    }
                }

                return response()->json([
                    'exchangeRate' => array_values($response["rates"]),
                ], 200);

                break;

            case 'currency_layer':
                $url = "http://api.currencylayer.com/live?access_key=".$request['key']."&source=INR&currencies=USD";
                $response = Http::get($url)->json();

                if (array_key_exists('success', $response)) {
                    if ($response["success"] == false) {
                        return respondJson($response["error"]["info"], $response["error"]["info"]);
                    }
                }

                return response()->json([
                    'exchangeRate' => array_values($response['quotes']),
                ], 200);

                break;

            case 'open_exchange_rate':
                $url = "https://openexchangerates.org/api/latest.json?app_id=".$request['key']."&base=INR&symbols=USD";
                $response = Http::get($url)->json();

                if (array_key_exists("error", $response)) {
                    return respondJson($response['message'], $response["description"]);
                }

                return response()->json([
                    'exchangeRate' => array_values($response["rates"]),
                ], 200);

                break;

            case 'currency_converter':
                $url = self::getCurrencyConverterUrl($request['driver_config']);
                $url = $url."/api/v7/convert?apiKey=".$request['key'];

                $query = "INR_USD";
                $url = $url."&q={$query}"."&compact=y";
                $response = Http::get($url)->json();

                return response()->json([
                    'exchangeRate' => array_values($response[$query]),
                ], 200);

                break;
        }
    }

    public static function getCurrencyConverterUrl($data)
    {
        switch ($data['type']) {
            case 'PREMIUM':
                return "https://api.currconv.com";

                break;

            case 'PREPAID':
                return "https://prepaid.currconv.com";

                break;

            case 'FREE':
                return "https://free.currconv.com";

                break;

            case 'DEDICATED':
                return $data['url'];

                break;
        }
    }
}
