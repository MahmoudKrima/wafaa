<?php

namespace App\Filters;

use Closure;

class CodeFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('code') && request()->input('code') != null) {
            $query->where('code', 'LIKE', '%' . request()->input('code') . '%');
        }
        return $next($query);
    }
}
