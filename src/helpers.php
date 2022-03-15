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


if (!function_exists('array_to_data_keys')) {

    function array_to_data_keys(array $array)
    {
        $result = [];
        # -----------
        foreach ($array as $key => $value) {
            $result[$key][] = $key;
            if (is_array($value) && \Illuminate\Support\Arr::isAssoc($value)) {
                foreach (array_to_data_keys($value) as $subkey) {
                    $result[$key][] = $subkey;
                }
            }
        }
        return array_values(array_map(function($keys) {
            return \join('.', $keys);
        }, $result));
    }
}