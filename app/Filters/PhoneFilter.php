<?php

namespace App\Filters;

use Closure;

class PhoneFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('phone') && request()->input('phone') != null) {
            $query->where('phone', 'LIKE', '%' . request()->input('phone') . '%');
        }
        return $next($query);
    }
}
