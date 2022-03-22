<?php

namespace Byancode\Settings;

use Illuminate\Support\Arr;
use Byancode\Settings\App\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private $cache;
    const key = 'app.settings';
    # ------------------------------
    public function __construct()
    {
        $this->cache = \app('cache');
    }

    public function tags()
    {
        $this->cache = \call_user_func_array([
            $this->cache, 'tags'
        ], \func_get_args());
        # -------------------
        return $this;
    }

    public function load()
    {
        try {
            return Setting::get()->mapWithKeys(function ($item) {
                return [$item['key'] => $item['value']];
            })->all();
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function all()
    {
        return $this->cache->rememberForever(self::key, function () {
            return $this->load();
        });
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
        try {
            Setting::getQuery()->updateOrInsert(
                compact('key'),
                compact('value')
            );
            $this->cache->flush();
            return true;
        } catch (\Throwable $th) {
            $this->cache->forever(self::key, $settings);
            return false;
        }
    }

    public function sync()
    {
        $settings = $this->all();
        # ------------------------
        foreach (\array_flaten_keys($settings) as  $keys) {
            \config([$keys => \data_get($settings, $keys)]);
        };
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
