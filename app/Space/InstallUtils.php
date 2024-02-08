<?php

namespace InvoiceShelf\Space;

use Illuminate\Database\QueryException;
use League\Flysystem\FilesystemException;

class InstallUtils
{
    /**
     * Check if database is created
     *
     * @return bool|int|string
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
            $flag = \Storage::disk('local')->has('database_created');
        } catch (FilesystemException $e) {
            $flag = false;
        }

        return $flag;
    }

    /**
     * Creates the database marker
     *
     * @return void
     */
    public static function createDbMarker()
    {
        \Storage::disk('local')->put('database_created', time());
    }

    /**
     * Deletes the database marker
     *
     * @return void
     */
    public static function deleteDbMarker()
    {
        try {
            \Storage::disk('local')->delete('database_created');
        } catch (\Exception $e) {
        }
    }
}
