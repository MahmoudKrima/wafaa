<?php

namespace App\Filters;

use Closure;

class SortFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('sorting') && request()->input('sorting') != null) {
            $query->orderBy('id', request()->input('sorting'));
        } else {
            $query->orderBy('id', 'desc');
        }
        return $next($query);
    }
}
