<?php

namespace App\Filters;

use Closure;

class FullNameFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('name') && request()->input('name') != null) {
            $query->where('first_name', 'LIKE', '%' . request()->input('name') . '%')
                ->orWhere('last_name', 'LIKE', '%' . request()->input('name') . '%');
        }
        return $next($query);
    }
}
