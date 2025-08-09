<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class ActiveAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard("admin")->check()) {
            return redirect()
                ->to(route('admin.auth.loginForm'));
        } elseif (Auth::guard("admin")->check() && Auth::guard("admin")->user()->status->value != 'active') {
            Auth::guard("admin")
                ->logout();
            return redirect()
                ->to(route('admin.auth.loginForm'));
        } elseif (!Auth::guard('admin')->user()) {
            return redirect()
                ->to(route('admin.auth.loginForm'));
        }
        return $next($request);
    }
}
