<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // STRONG authentication check with session validation
        if (!Auth::check()) {
            // Force logout and clear everything
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Clear all cookies
            return redirect()->route('admin.login')
                ->withCookies([
                    cookie()->forget('laravel_session'),
                    cookie()->forget('XSRF-TOKEN'),
                ])
                ->with('error', 'Session expired. Please login.');
        }
        
        // Additional check: Make sure user exists in database
        $user = Auth::user();
        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('admin.login')
                ->with('error', 'User not found. Please login again.');
        }
        
        // Log access for debugging
        \Log::info('Dashboard accessed by user', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => $request->ip(),
            'time' => now()
        ]);
        
        return view('admin.dashboard');
    }
}