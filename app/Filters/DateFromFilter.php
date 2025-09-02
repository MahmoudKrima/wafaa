<?php

namespace App\Filters;

use Closure;
use Carbon\Carbon;

class DateFromFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('date_from') && request()->input('date_from') != null) {
            $start = Carbon::parse(request('date_from'), 'Asia/Riyadh')
                ->startOfDay()
                ->utc();
            $query->where('created_at', '>=', $start);
        }
        return $next($query);
    }
}
