<?php

use App\Models\Setting;

if (!function_exists('app_setting')) {
    function app_setting($key, $default = null) {
        $setting = Setting::find($key);
        return $setting ? $setting->value : $default;
    }
}