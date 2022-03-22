<?php

namespace Byancode\Settings;

use Byancode\Settings\App\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Settings
{
    const key = 'app.settings';

    public function load()
    {
        try {
            return Setting::get()->mapWithKeys(function ($item) {
                return [$item['key'] => $item['value']];
            })->all();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function all()
    {
        return Cache::get(self::key) ?? $this->sync();
    }

    public function update(array $data, bool $override = true)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value, $override);
        }
    }

    public function get(string $key = '*', $default = null)
    {
        $key = str_replace('__', '.', $key);
        return \data_get($this->all(), $key) ?? \config($key) ?? $default;
    }

    public function set(string $keys, $data, bool $override = true): bool
    {
        $keys = str_replace('__', '.', $keys);
        $key = strstr($keys, '.', true);
        # -------------------------
        $settings = $this->all();
        # -------------------------
        \data_set($settings, $keys, $data, $override);
        \config([$keys => $data]);
        # -------------------------
        $value = json_encode($settings[$key]);
        # -------------------------
        Cache::forever(self::key, $settings);
        # -------------------------
        try {
            Setting::getQuery()->updateOrInsert(
                compact('key'),
                compact('value')
            );
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function sync()
    {
        $settings = $this->load();
        # ------------------------
        if (is_null($settings)) {
            return [];
        }
        # ------------------------
        \config(\array_flaten_keys($settings));
        # ------------------------
        Cache::forever(self::key, $settings);
        # ------------------------
        return $settings;
    }

    public function push(string $key, $value): bool
    {
        $data = $this->get($key);
        # -----------------------
        if (\is_array($data)) {
            $data[] = $value;
        }
        # -----------------------
        return $this->set($key, $data);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }
}
