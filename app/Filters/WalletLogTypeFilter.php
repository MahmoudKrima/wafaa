<?php

namespace App\Filters;

use Closure;

class WalletLogTypeFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('trans_type') && request()->input('trans_type') != null) {
            $query->where('trans_type', request()->input('trans_type'));
        }
        return $next($query);
    }
}
