<?php

namespace App\Filters;

use Closure;

class CityFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('city') && request()->input('city') != null) {
            $query->where('city_id', request()->input('city'));
        }
        return $next($query);
    }
}
