<?php

namespace App\Filters;

use Closure;

class RoleFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('role') && request()->input('role') != null) {
            $query->whereHas('roles', function ($ro) {
                $ro->where('role_id', request()->input('role'))
                    ->where('guard_name', 'admin');
            });
        }
        return $next($query);
    }
}
