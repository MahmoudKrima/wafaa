<?php

namespace App\Filters;

use Closure;

class EmailFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('email') && request()->input('email') != null) {
            $query->where('email', 'LIKE', '%' . request()->input('email') . '%');
        }
        return $next($query);
    }
}
