<?php
namespace Crater\Space;

use File;
use ZipArchive;
use Artisan;
use GuzzleHttp\Exception\RequestException;
use Crater\Space\SiteApi;
use Crater\Events\UpdateFinished;
use Crater\Setting;
use Illuminate\Http\Request;

class Updater
{
    use SiteApi;

    public static function update($installed, $version)
    {
        $data = null;
        $path = null;

        if(env('APP_ENV') === 'development')
        {
            $url = 'https://craterapp.com/downloads/file/'.$version.'?type=update&is_dev=1';
        } else {
            $url = 'https://craterapp.com/downloads/file/'.$version.'?type=update';
        }


        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        // Exception
        if ($response instanceof RequestException) {
            return [
                'success' => false,
                'error' => 'Download Exception',
                'data' => [
                    'path' => $path
                ]
            ];
        }

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        // Create temp directory
        $path = 'temp-' . md5(mt_rand());
        $path2 = 'temp2-' . md5(mt_rand());
        $temp_path = storage_path('app') . '/' . $path;
        $temp_path2 = storage_path('app') . '/' . $path2;

        if (!File::isDirectory($temp_path)) {
            File::makeDirectory($temp_path);
            File::makeDirectory($temp_path2);
        }

        try {

            $file = $temp_path . '/upload.zip';

            // Add content to the Zip file
            $uploaded = is_int(file_put_contents($file, $data)) ? true : false;

            if (!$uploaded) {
                return false;
            }

            // Unzip the file
            $zip = new ZipArchive();

            if ($zip->open($file)) {
                $zip->extractTo($temp_path2);
            }

            $zip->close();

            // Delete zip file
            File::delete($file);

            if (!File::copyDirectory($temp_path2.'/Crater', base_path())) {
                return false;
            }

            // Delete temp directory
            File::deleteDirectory($temp_path);
            File::deleteDirectory($temp_path2);

            return [
                'success' => true,
                'error' => false,
                'data' => []
            ];
        } catch (\Exception $e) {

            if (File::isDirectory($temp_path)) {
                // Delete temp directory
                File::deleteDirectory($temp_path);
                File::deleteDirectory($temp_path2);
            }

            return [
                'success' => false,
                'error' => 'Update error',
                'data' => []
            ];
        }
    }

    public static function finishUpdate($installed, $version)
    {
        event(new UpdateFinished($installed, $version));

        return [
            'success' => true,
            'error' => false,
            'data' => []
        ];
    }

    public static function checkForUpdate()
    {
        $data = null;
        if(env('APP_ENV') === 'development')
        {
            $url = 'https://craterapp.com/downloads/check/latest/'. Setting::getSetting('version') . '?type=update&is_dev=1';
        } else {
            $url = 'https://craterapp.com/downloads/check/latest/'. Setting::getSetting('version') . '?type=update';
        }

        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        return json_decode($data);
    }
}
