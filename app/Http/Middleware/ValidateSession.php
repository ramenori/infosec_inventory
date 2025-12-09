<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Additional validation if needed
            if (!$user) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('admin.login');
            }
        }
        
        return $next($request);
    }
}