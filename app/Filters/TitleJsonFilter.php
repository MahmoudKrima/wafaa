<?php

namespace App\Filters;

use Closure;

class TitleJsonFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('title') && request()->input('title') != null) {
            $title = request()->input('title');
            $locale = app()->getLocale();
            if (app()->getLocale() == 'ar') {
                $locale = 'ar';
                $query->where("title->{$locale}", 'LIKE', '%' . $title . '%');
            } else {
                $locale = 'en';
                $query->where("title->{$locale}", 'LIKE', '%' . $title . '%');
            }
        }
        return $next($query);
    }
}
