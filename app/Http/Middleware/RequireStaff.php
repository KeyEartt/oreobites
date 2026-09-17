<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RequireStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = Session::get('auth_user');

        if (!$user) {
            return redirect('/login')->with('error', 'Please log in first.');
        }

        if (!in_array($user['role'], ['staff', 'admin'])) {
            return redirect('/')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}