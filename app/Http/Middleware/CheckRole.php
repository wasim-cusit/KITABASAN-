<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Redirect to appropriate dashboard based on user's role instead of aborting
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // If user has no role, redirect to home
        return redirect()->route('home')
            ->with('error', 'You do not have permission to access this page.');
    }
}
