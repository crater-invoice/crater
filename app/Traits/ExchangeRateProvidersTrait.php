<?php

namespace Crater\Traits;

use Illuminate\Support\Facades\Http;

trait ExchangeRateProvidersTrait
{
    public function getExchangeRate($filter, $baseCurrencyCode, $currencyCode)
    {
        switch ($filter['driver']) {
            case 'currency_freak':
                $url = "https://api.currencyfreaks.com/latest?apikey=".$filter['key'];

                $url = $url."&symbols={$currencyCode}"."&base={$baseCurrencyCode}";
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
                $url = "http://api.currencylayer.com/live?access_key=".$filter['key']."&source={$baseCurrencyCode}&currencies={$currencyCode}";
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
                $url = "https://openexchangerates.org/api/latest.json?app_id=".$filter['key']."&base={$baseCurrencyCode}&symbols={$currencyCode}";
                $response = Http::get($url)->json();

                if (array_key_exists("error", $response)) {
                    return respondJson($response["message"], $response["description"]);
                }

                return response()->json([
                    'exchangeRate' => array_values($response["rates"]),
                ], 200);

                break;

            case 'currency_converter':
                $url = $this->getCurrencyConverterUrl($filter['driver_config']);
                $url = $url."/api/v7/convert?apiKey=".$filter['key'];

                $query = "{$baseCurrencyCode}_{$currencyCode}";
                $url = $url."&q={$query}"."&compact=y";
                $response = Http::get($url)->json();

                return response()->json([
                    'exchangeRate' => array_values($response[$query]),
                ], 200);

                break;
        }
    }

    public function getCurrencyConverterUrl($data)
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

    public function getSupportedCurrencies($request)
    {
        $message = 'Please Enter Valid Provider Key.';
        $error = 'invalid_key';

        $server_message = 'Server not responding';
        $error_message = 'server_error';

        switch ($request->driver) {
            case 'currency_freak':
                $url = "https://api.currencyfreaks.com/currency-symbols";
                $response = Http::get($url)->json();
                $checkKey = $this->getUrl($request);

                if ($response == null || $checkKey == null) {
                    return respondJson($error_message, $server_message);
                }

                if (array_key_exists('success', $checkKey) && array_key_exists('error', $checkKey)) {
                    if ($checkKey['error']['status'] == 404) {
                        return respondJson($error, $message);
                    }
                }

                return response()->json(['supportedCurrencies' => array_keys($response)]);

                break;

            case 'currency_layer':
                $url = "http://api.currencylayer.com/list?access_key=".$request->key;
                $response = Http::get($url)->json();

                if ($response == null) {
                    return respondJson($error_message, $server_message);
                }

                if (array_key_exists('currencies', $response)) {
                    return response()->json(['supportedCurrencies' => array_keys($response['currencies'])]);
                }

                return respondJson($error, $message);

                break;

            case 'open_exchange_rate':
                $url = "https://openexchangerates.org/api/currencies.json";
                $response = Http::get($url)->json();
                $checkKey = $this->getUrl($request);

                if ($response == null || $checkKey == null) {
                    return respondJson($error_message, $server_message);
                }

                if (array_key_exists('error', $checkKey)) {
                    if ($checkKey['status'] == 401) {
                        return respondJson($error, $message);
                    }
                }

                return response()->json(['supportedCurrencies' => array_keys($response)]);

                break;

            case 'currency_converter':
                $response = $this->getUrl($request);

                if ($response == null) {
                    return respondJson($error_message, $server_message);
                }

                if (array_key_exists('results', $response)) {
                    return response()->json(['supportedCurrencies' => array_keys($response['results'])]);
                }

                return respondJson($error, $message);

                break;
        }
    }

    public function getUrl($request)
    {
        switch ($request->driver) {
            case 'currency_freak':
                $url = "https://api.currencyfreaks.com/latest?apikey=".$request->key."&symbols=INR&base=USD";

                return Http::get($url)->json();

                break;

            case 'currency_layer':
                $url = "http://api.currencylayer.com/live?access_key=".$request->key."&source=INR&currencies=USD";

                return Http::get($url)->json();

                break;

            case 'open_exchange_rate':
                $url = "https://openexchangerates.org/api/latest.json?app_id=".$request->key."&base=INR&symbols=USD";

                return Http::get($url)->json();

                break;

            case 'currency_converter':
                $url = $this->getCurrencyConverterUrl($request)."/api/v7/currencies?apiKey=".$request->key;

                return Http::get($url)->json();

                break;
        }
    }
}
