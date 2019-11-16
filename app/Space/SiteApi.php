<?php

namespace Crater\Space;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Crater\Setting;

trait SiteApi
{

    protected static function getRemote($url, $data = array())
    {
        $base = 'https://craterapp.com/';

        $client = new Client(['verify' => false, 'base_uri' => $base]);

        $headers['headers'] = array(
            'Accept'        => 'application/json',
            'Referer'       => url('/'),
            'crater'        => Setting::getSetting('version')
        );

        $data['http_errors'] = false;

        $data = array_merge($data, $headers);

        try {
            $result = $client->get($url, $data);
        } catch (RequestException $e) {
            $result = $e;
        }

        return $result;
    }
}
