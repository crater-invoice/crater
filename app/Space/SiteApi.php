<?php

namespace Laraspace\Space;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait SiteApi
{

    protected static function getRemote($url, $data = array())
    {
        $base = 'https://codeload.github.com/';

        $client = new Client(['verify' => false, 'base_uri' => $base]);

        $headers['headers'] = array(
            'Accept'        => 'application/json',
            'Referer'       => url('/'),
            'crater'        => getFullVersion()
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
