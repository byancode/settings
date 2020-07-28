<?php

namespace Byancode\Settings;

use Illuminate\Support\Arr;
use Byancode\Settings\App\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private $appCache;
    const cacheName = 'app.settings';
    # ------------------------------
    public function __construct()
    {
        $this->appCache = \app('cache');
    }
    
    public function tags()
    {
        $this->appCache = \call_user_func_array([
            $this->appCache, 'tags'
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
        return $this->appCache->rememberForever(self::cacheName, function () {
            return $this->load();
        });
    }

    public function get(string $key = null, $default = null)
    {
        $key = str_replace('__', '.', $key);
        return \data_get($this->all(), $key) ?? \config($key) ?? $default;
    }

    public function set(string $key, $value, bool $override = true) : bool
    {
        $key = str_replace('__', '.', $key);
        $keybase = \explode('.', $key);
        $keybase = \current($keybase);
        # -------------------------
        $settings = $this->all();
        # -------------------------
        \data_set($settings, $key, $value, $override);
        # -------------------------
        $data = \array_map(function($value){
            return \json_encode($value) ?? $value;
        }, $settings);
        # -------------------------
        try {
            Setting::getQuery()->updateOrInsert($keybase == '*' ? $data : $data[$keybase]);
            self::flush();
            return true;
        } catch (\Throwable $th) {
            $this->appCache->forever(self::cacheName, $settings);
            return false;
        }
    }

    public function sync()
    {
        $settings = $this->all();
        # ------------------------
        foreach (\array_to_data_keys($settings) as  $keys) {
            \config([ $keys => \data_get($settings, $keys) ]);
        };
    }

    public function push(string $key, $value) : bool
    {
        $data = $this->get($key);
        # -----------------------
        if (\is_array($data)) {
            $data[] = $value;
        }
        # -----------------------
        return $this->set($key, $data);
    }

    public function flush()
    {
        Cache::forget(self::cacheName);
        return $this->appCache->flush();
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