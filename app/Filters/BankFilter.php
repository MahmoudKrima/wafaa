<?php

namespace App\Filters;

use Closure;

class BankFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('bank') && request()->input('bank') != null) {
            $query->where('banks_id', request()->input('bank'));
        }
        return $next($query);
    }
}
