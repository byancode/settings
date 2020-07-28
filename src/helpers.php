<?php

if (!function_exists('settings')) {

    function settings($key = null, $value = null, bool $override = true)
    {

        if (empty($key))
            return app('settings');

        if (!empty($key) && !empty($value))
            return app('settings')->set($key, $value, $override);

        return app('settings')->get($key);
    }
}
