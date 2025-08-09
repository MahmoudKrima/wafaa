<?php

namespace App\Filters;

use Closure;

class JobTitleJsonFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('job_title') && request()->input('job_title') != null) {
            $jobTitle = request()->input('job_title');
            $locale = app()->getLocale();
            if (app()->getLocale() == 'ar') {
                $locale = 'ar';
                $query->where("job_title->{$locale}", 'LIKE', '%' . $jobTitle . '%');
            } else {
                $locale = 'en';
                $query->where("job_title->{$locale}", 'LIKE', '%' . $jobTitle . '%');
            }
        }
        return $next($query);
    }
}
