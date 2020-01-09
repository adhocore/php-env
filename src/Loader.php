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
 * DotEnv loader for PHP.
 *
 * @author   Jitendra Adhikari <jiten.adhikary@gmail.com>
 * @license  MIT
 *
 * @link     https://github.com/adhocore/env
 */
class Loader
{
    /**
     * Put the parsed key value pair into $_ENV superglobal.
     */
    const ENV    = 1;

    /**
     * Put the parsed key value pair into putenv().
     */
    const PUTENV = 2;

    /**
     * Put the parsed key value pair into $_SERVER superglobal.
     */
    const SERVER = 4;

    /**
     * Put the parsed key value pair into all of the sources.
     */
    const ALL    = 7;

    /**
     * Loads .env file and puts the key value pair in one or all of putenv()/$_ENV/$_SERVER.
     *
     * @param string $file     The full path of .env file.
     * @param bool   $override Whether to override already available env key.
     * @param int    $mode     Where to load the env vars. Defaults to putenv(). Use pipe for multiple.
     *                         Example: Loader::SERVER | Loader::ENV | Loader::PUTENV
     *
     * @throws \InvalidArgumentException If the file does not exist or cant be read.
     * @throws \RuntimeException         If the file content cant be parsed.
     */
    public function load($file, $override = false, $mode = self::PUTENV)
    {
        if (!\is_file($file)) {
            throw new \InvalidArgumentException('The .env file does not exist or is not readable');
        }

        // Get file contents, fix the comments and parse as ini.
        $content = \preg_replace('/^\s*#/m', ';', \file_get_contents($file));
        $parsed  = \parse_ini_string($content, false, INI_SCANNER_RAW);

        if ($parsed === false) {
            throw new \RuntimeException('The .env file cannot be parsed due to malformed values');
        }

        $this->setEnv($parsed, (bool) $override, (int) $mode);
    }

    /**
     * Set the env values from given vars.
     *
     * @param array $vars
     * @param bool  $override
     * @param int   $mode
     */
    protected function setEnv(array $vars, $override, $mode = self::PUTENV)
    {
        $default = \microtime(1);

        foreach ($vars as $key => $value) {
            // Skip if we already have value and cant override.
            if (!$override && $default !== Retriever::getEnv($key, $default)) {
                continue;
            }

            $this->set($key, $value, $mode);
        }

        $this->resolveRefs($vars, $mode);
    }

    protected function set($key, $value, $mode)
    {
        $this->toEnv($key, $value, $mode);
        $this->toServer($key, $value, $mode);
        $this->toPutenv($key, $value, $mode);
    }

    protected function resolveRefs($vars, $mode)
    {
        foreach ($vars as $key => $value) {
            if (!$value || \strpos($value, '${') === false) {
                continue;
            }

            $value = \preg_replace_callback('~\$\{(\w+)\}~', function ($m) {
                return (null === $ref = Retriever::getEnv($m[1], null)) ? $m[0] : $ref;
            }, $value);

            $this->set($key, $value, $mode);
        }
    }

    private function toEnv($key, $value, $mode)
    {
        if ($mode & self::ENV) {
            $_ENV[$key] = $value;
        }
    }

    private function toServer($key, $value, $mode)
    {
        if ($mode & self::SERVER) {
            $_SERVER[$key] = $value;
        }
    }

    private function toPutenv($key, $value, $mode)
    {
        if ($mode & self::PUTENV) {
            \putenv("$key=$value");
        }
    }
}
