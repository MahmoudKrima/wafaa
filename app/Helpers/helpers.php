<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (!function_exists('displayImage')) {
    function displayImage($object)
    {
        if (filter_var($object, FILTER_VALIDATE_URL)) {
            return $object;
        }

        if (Storage::disk('public')->exists($object)) {
            return asset('storage/' . ltrim($object, '/'));
        }

        if (file_exists(public_path($object))) {
            return asset($object);
        }

        return asset('defaults/1.png');
    }
}

if (!function_exists('isRoute')) {
    function isRoute(array $routes)
    {
        foreach ($routes as $route) {
            if (Route::is($route)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('activeLink')) {
    function activeLink(string $route)
    {
        if (Route::is($route)) {
            return 'active';
        }
        return '';
    }
}

if (!function_exists('assetLang')) {
    function assetLang()
    {
        if (app()->getLocale() == 'ar') {
            return 'ar';
        } else {
            return 'en';
        }
    }
}


if (!function_exists('createSlug')) {
    function createSlug($string)
    {
        return str_replace(' ', '-', strtolower($string));
    }
}

if (!function_exists('getAdminIdOrCreatedBy')) {
    function getAdminIdOrCreatedBy()
    {
        if (auth('admin')->check()) {
            $admin = auth('admin')->user();
            if ($admin->hasRole('administrator')) {
                return $admin->id;
            }

            return $admin->created_by ?? $admin->id;
        }

        return null;
    }
}
