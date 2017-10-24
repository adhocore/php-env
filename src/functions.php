<?php

// @codeCoverageIgnoreStart
if (!function_exists('env')) {
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
    function env($key, $default = null, $filter = null, $options = null)
    {
        return \Ahc\Env\Retriever::getEnv($key, $default, $filter, $options);
    }
}
// @codeCoverageIgnoreEnd
