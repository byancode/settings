<?php

if (!function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            return app('settings')->update($key);
        }

        return app('settings')->get($key, $default);
    }
}

if (!function_exists('settings')) {

    function settings($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            return app('settings')->update($key);
        }

        return app('settings')->get($key, $default);
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
