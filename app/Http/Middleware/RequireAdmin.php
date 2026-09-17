<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RequireAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Session::get('auth_user');

        if (!$user) {
            return redirect('/login')->with('error', 'Please log in first.');
        }

        if ($user['role'] !== 'admin') {
            return redirect('/')->with('error', 'Admins only.');
        }

        return $next($request);
    }
}