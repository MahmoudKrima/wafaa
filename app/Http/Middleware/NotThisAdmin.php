<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class NotThisAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = $request->route('admin')->id;
        if ($adminId == Auth::guard('admin')->user()->id) {
            return back()
                ->with('Error', __('admin.not_allowed'));
        }

        return $next($request);
    }
}
