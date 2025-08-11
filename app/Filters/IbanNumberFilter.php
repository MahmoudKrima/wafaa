<?php

namespace App\Filters;

use Closure;

class IbanNumberFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('iban_number') && request()->input('iban_number') != null) {
            $query->where('iban_number', 'LIKE', '%' . request()->input('iban_number') . '%');
        }
        return $next($query);
    }
}
