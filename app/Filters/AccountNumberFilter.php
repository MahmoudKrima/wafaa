<?php

namespace App\Filters;

use Closure;

class AccountNumberFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('account_number') && request()->input('account_number') != null) {
            $query->where('account_number', 'LIKE', '%' . request()->input('account_number') . '%');
        }
        return $next($query);
    }
}
