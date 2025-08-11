<?php

namespace App\Filters;

use Closure;

class AccountOwnerFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('account_owner') && request()->input('account_owner') != null) {
            $account_owner = request()->input('account_owner');
            $locale = app()->getLocale();
            if (app()->getLocale() == 'ar') {
                $locale = 'ar';
                $query->where("account_owner->{$locale}", 'LIKE', '%' . $account_owner . '%');
            } else {
                $locale = 'en';
                $query->where("account_owner->{$locale}", 'LIKE', '%' . $account_owner . '%');
            }
        }
        return $next($query);
    }
}
