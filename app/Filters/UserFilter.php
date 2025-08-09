<?php

namespace App\Filters;

use Closure;

class UserFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('user_id') && request()->input('user_id') != null) {
            $query->where('user_id', request()->input('user_id'));
        }
        return $next($query);
    }
}
