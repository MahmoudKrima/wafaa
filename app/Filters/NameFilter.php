<?php

namespace App\Filters;

use Closure;

class NameFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('name') && request()->input('name') != null) {
            $query->where('name', 'LIKE', '%' . request()->input('name') . '%');
        }
        return $next($query);
    }
}
