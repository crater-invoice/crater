<?php

namespace Crater\Space;

use Artisan;
use Crater\Events\UpdateFinished;
use File;
use GuzzleHttp\Exception\RequestException;
use ZipArchive;

// Implementation taken from Akaunting - https://github.com/akaunting/akaunting
class Updater
{
    use SiteApi;

    public static function checkForUpdate($installed_version)
    {
        $data = null;
        if (env('APP_ENV') === 'development' || env('APP_ENV') === 'local') {
            $url = 'downloads/check/latest/'.$installed_version.'?type=update&is_dev=1';
        } else {
            $url = 'downloads/check/latest/'.$installed_version.'?type=update';
        }

        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        $data = json_decode($data);

        if ($data->success && $data->version && property_exists($data->version, 'extensions')) {
            $extensions = $data->version->extensions;
            $extensionData = [];
            foreach (json_decode($extensions) as $extension) {
                $extensionData[$extension] = phpversion($extension) ? true : false;
            }
            $extensionData['php'.'('.$data->version->minimum_php_version.')'] = version_compare(phpversion(), $data->version->minimum_php_version, ">=");
            $data->version->extensions = $extensionData;
        }

        return $data;
    }

    public static function download($new_version, $is_cmd = 0)
    {
        $data = null;
        $path = null;

        if (env('APP_ENV') === 'development') {
            $url = 'downloads/file/'.$new_version.'?type=update&is_dev=1&is_cmd='.$is_cmd;
        } else {
            $url = 'downloads/file/'.$new_version.'?type=update&is_cmd='.$is_cmd;
        }

        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        // Exception
        if ($response instanceof RequestException) {
            return [
                'success' => false,
                'error' => 'Download Exception',
                'data' => [
                    'path' => $path,
                ],
            ];
        }

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        // Create temp directory
        $temp_dir = storage_path('app/temp-'.md5(mt_rand()));

        if (! File::isDirectory($temp_dir)) {
            File::makeDirectory($temp_dir);
        }

        $zip_file_path = $temp_dir.'/upload.zip';

        // Add content to the Zip file
        $uploaded = is_int(file_put_contents($zip_file_path, $data)) ? true : false;

        if (! $uploaded) {
            return false;
        }

        return $zip_file_path;
    }

    public static function unzip($zip_file_path)
    {
        if (! file_exists($zip_file_path)) {
            throw new \Exception('Zip file not found');
        }

        $temp_extract_dir = storage_path('app/temp2-'.md5(mt_rand()));

        if (! File::isDirectory($temp_extract_dir)) {
            File::makeDirectory($temp_extract_dir);
        }
        // Unzip the file
        $zip = new ZipArchive();

        if ($zip->open($zip_file_path)) {
            $zip->extractTo($temp_extract_dir);
        }

        $zip->close();

        // Delete zip file
        File::delete($zip_file_path);

        return $temp_extract_dir;
    }

    public static function copyFiles($temp_extract_dir)
    {
        if (! File::copyDirectory($temp_extract_dir.'/Crater', base_path())) {
            return false;
        }

        // Delete temp directory
        File::deleteDirectory($temp_extract_dir);

        return true;
    }

    public static function deleteFiles($json)
    {
        $files = json_decode($json);

        foreach ($files as $file) {
            \File::delete(base_path($file));
        }

        return true;
    }

    public static function migrateUpdate()
    {
        Artisan::call('migrate --force');

        return true;
    }

    public static function finishUpdate($installed, $version)
    {
        event(new UpdateFinished($installed, $version));

        return [
            'success' => true,
            'error' => false,
            'data' => [],
        ];
    }
}
