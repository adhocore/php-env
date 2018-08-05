<?php

/*
 * This file is part of the PHP-ENV package.
 *
 * (c) Jitendra Adhikari <jiten.adhikary@gmail.com>
 *     <https://github.com/adhocore>
 *
 * Licensed under MIT license.
 */

namespace Ahc\Env;

/**
 * Environment variable retriever for PHP.
 *
 * @author   Jitendra Adhikari <jiten.adhikary@gmail.com>
 * @license  MIT
 *
 * @link     https://github.com/adhocore/env
 */
class Retriever
{
    /**
     * Get the env variable by its key/name.
     *
     * @param string    $key
     * @param mixed     $default
     * @param int|null  $filter  PHP's filter constants. See http://php.net/filter_var
     * @param int|array $options Additional options to filter.
     *
     * @return mixed
     */
    public static function getEnv($key, $default = null, $filter = null, $options = null)
    {
        if (false !== $env = \getenv($key)) {
            return static::prepareValue($env, $filter, $options);
        }

        if (isset($_ENV[$key])) {
            return static::prepareValue($_ENV[$key], $filter, $options);
        }

        if (isset($_SERVER[$key])) {
            return static::prepareValue($_SERVER[$key], $filter, $options);
        }

        // Default is not passed through filter!
        return $default;
    }

    protected static function prepareValue($env, $filter, $options)
    {
        static $special = [
            'true' => true, 'false' => false, 'null' => null,
            'TRUE' => true, 'FALSE' => false, 'NULL' => null,
        ];

        // strlen($env) < 6.
        if (!isset($env[5]) && \array_key_exists($env, $special)) {
            return $special[$env];
        }

        if ($filter === null || !\function_exists('filter_var')) {
            return $env;
        }

        return \filter_var($env, $filter, $options);
    }
}
