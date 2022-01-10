<?php

namespace Crater\Services\Module;

class Module
{
    /**
     * All of the registered Modules scripts.
     *
     * @var array
     */
    public static $scripts = [];

    /**
     * All of the registered company settings.
     *
     * @var array
     */
    public static $settings = [];

    /**
     * All of the registered Modules CSS.
     *
     * @var array
     */
    public static $styles = [];

    /**
     * Register the given script file with Module.
     *
     * @param  string  $name
     * @param  string  $path
     * @return static
     */
    public static function script($name, $path)
    {
        static::$scripts[$name] = $path;

        return new static();
    }

    /**
     * Register the given CSS file with Module.
     *
     * @param  string  $name
     * @param  string  $path
     * @return static
     */
    public static function style($name, $path)
    {
        static::$styles[$name] = $path;

        return new static();
    }

    /**
     * Get all of the additional scripts that should be registered.
     *
     * @return array
     */
    public static function allScripts()
    {
        return static::$scripts;
    }

    /**
     * Get all of the additional stylesheets that should be registered.
     *
     * @return array
     */
    public static function allStyles()
    {
        return static::$styles;
    }
}
