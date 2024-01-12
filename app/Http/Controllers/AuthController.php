<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller {
    
    public function showLoginForm() {
        if(Auth::check()){
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        $hashedPassword = md5($credentials['password']);
        $user = User::where('login', $credentials['username'])->first();
        
        if ($user && $hashedPassword === $user->password) {
            Auth::login($user, true);
            $sessionLifetime = Config::get('session.lifetime');
            Session::put('expires_at', now()->addMinutes($sessionLifetime));
            return redirect()->intended(route('admin.dashboard'));
        }
    
        return redirect()->route('sodadmin.login')->with('error', 'Invalid login credentials');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('sodadmin.login');
    }
}
