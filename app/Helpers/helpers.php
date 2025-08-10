<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (!function_exists('displayImage')) {
    function displayImage($object)
    {
        if (Storage::exists('public/' . $object)) {
            return url(asset('storage/' . $object));
        }
        return url(asset($object));
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
