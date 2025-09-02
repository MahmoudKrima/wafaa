<?php

namespace App\Filters;

use Closure;

class TransActionFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('type') && request()->input('type') != null) {
            $query->where('type', request()->input('type'));
        }
        return $next($query);
    }
}
