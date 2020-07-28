<?php

namespace Byancode\Settings;

use App\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private $cacheSetting;
    const cacheName = 'app.settings';
    # ------------------------------
    public function __construct()
    {
        $this->appCache = \app('cache');
    }
    # ------------------------------
    public function tags()
    {
        $this->appCache = \call_user_func_array([
            $this, 'tags'
        ], \func_get_args());
        # -------------------
        return $this;
    }
    # ------------------------------
    public function all()
    {
        return $this->appCache->rememberForever(self::cacheName, function () {
            try {
                return Setting::get()->mapWithKeys(function ($item) {
                    return [$item['key'] => $item['value']];
                })->all();
            } catch (\Throwable $th) {
                return [];
            }
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
        \data_set($settings, $key, $override);
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
            $this->appCache->forever(self::cacheName, $data);
            return false;
        }
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

    public static function flush()
    {
        return Cache::forget(self::cacheName);
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}