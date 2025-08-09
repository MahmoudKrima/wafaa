<?php

namespace App\Filters;

use Closure;

class NameJsonFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('name') && request()->input('name') != null) {
            $name = request()->input('name');
            $locale = app()->getLocale();
            if (app()->getLocale() == 'ar') {
                $locale = 'ar';
                $query->where("name->{$locale}", 'LIKE', '%' . $name . '%');
            } else {
                $locale = 'en';
                $query->where("name->{$locale}", 'LIKE', '%' . $name . '%');
            }
        }
        return $next($query);
    }
}
