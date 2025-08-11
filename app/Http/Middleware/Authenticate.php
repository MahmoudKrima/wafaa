<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check which guard is being used and redirect accordingly
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            return route('admin.auth.loginForm');
        }

        if ($request->is('user/*') || $request->is('*/user/*')) {
            return route('user.auth.loginForm');
        }

        // Default fallback
        return route('admin.auth.loginForm');
    }
}
