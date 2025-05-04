<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function loginPost(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();

                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard')
                        ->with('success', 'Welcome to the Dashboard!');
                }

                if ($user->role === 'shop') {
                    return redirect()->route('shop.dashboard')
                        ->with('success', 'Welcome to your Dashboard!');
                }
                Auth::logout();
                return redirect()->route('admin.login')
                    ->with('error', 'You are not authorized to access!');
            }

            return back()->withErrors(['email' => 'Invalid credentials.']);

        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'An error occurred. Please try again later.'
            ]);
        }
    }
    public function logout()
    {
        try {
            Auth::logout();

            return redirect()->route('admin.login')->with(
                'status',
                'Successfully logged out!'
            );
        } catch (Exception $e) {
            Log::error("Logout error: " . $e->getMessage());

            return back()->with('error', 'An error occurred while logging out.');
        }
    }
}
