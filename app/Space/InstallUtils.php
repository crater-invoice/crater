<?php

namespace InvoiceShelf\Space;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FilesystemException;

class InstallUtils
{
    /**
     * Check if database is created
     *
     * @return bool
     */
    public static function isDbCreated()
    {
        return self::dbMarkerExists() && self::tableExists('users');
    }

    /**
     * Check if database is created
     *
     * @return bool|int|string
     */
    public static function tableExists($table)
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        try {
            $flag = \Schema::hasTable($table);
        } catch (QueryException|\Exception $e) {
            $flag = false;
        }

        $cache[$table] = $flag;

        return $cache[$table];
    }

    /**
     * Check if database created marker exists
     *
     * @return bool
     */
    public static function dbMarkerExists()
    {
        try {
            return \Storage::disk('local')->has('database_created');
        } catch (FilesystemException $e) {
            Log::error('Unable to verify db marker: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Creates the database marker
     *
     * @return bool
     */
    public static function createDbMarker()
    {
        try {
            return \Storage::disk('local')->put('database_created', time());
        } catch (\Exception $e) {
            Log::error('Unable to create db marker: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Deletes the database marker
     *
     * @return bool
     */
    public static function deleteDbMarker()
    {
        try {
            return \Storage::disk('local')->delete('database_created');
        } catch (\Exception $e) {
            Log::error('Unable to delete db marker: '.$e->getMessage());
        }

        return false;
    }
}
