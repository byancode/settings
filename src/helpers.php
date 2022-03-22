<?php

if (!function_exists('setting')) {
    function setting($key = null, $value = null, bool $override = true)
    {
        return settings($key, $value, $override);
    }
}

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

if (!function_exists('array_flaten_keys')) {
    function array_flaten_keys(array $array, array $subs = [])
    {
        $result = [];
        foreach ($array as $key => $value) {
            $list = $subs;
            $list[] = $key;
            $keys = join('.', $list);
            if (is_array($value)) {
                $result += array_flaten_keys($value, [$keys]);
            } else {
                $result[$keys] = $value;
            }
        }
        return $result;
    }
}
