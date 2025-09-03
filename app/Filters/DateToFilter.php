<?php

namespace App\Filters;

use Closure;
use Carbon\Carbon;

class DateToFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('date_to') && request()->input('date_to') != null) {
            $end = Carbon::parse(request('date_to'), 'Asia/Riyadh')
                ->endOfDay()
                ->utc();
            $query->where('created_at', '<=', $end);
        }
        return $next($query);
    }
}
