<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(Request $request)
    {
        // Store the intended URL if provided
        if ($request->has('redirect')) {
            $request->session()->put('url.intended', $request->get('redirect'));
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Check if staff account is approved (admins and employees only)
            if (in_array($user->user_type, ['admin', 'employee']) && !$user->is_approved) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval. Please contact an administrator.',
                ])->onlyInput('email');
            }
            
            // Check if user needs to change password
            if ($user->password_change_required) {
                return redirect()->route('password.change')
                    ->with('warning', 'You must change your temporary password before continuing.');
            }
            
            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($user->isEmployee()) {
                return redirect()->intended(route('employee.dashboard'))
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            } else {
                return redirect()->intended(route('courses.index'))
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'))
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the staff registration form.
     */
    public function showStaffRegister()
    {
        return view('auth.staff-register');
    }

    /**
     * Handle staff registration request.
     */
    public function staffRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', 'in:admin,employee'],
        ]);

        // Check if this is the first admin (no approved admins exist)
        $isFirstAdmin = !User::hasAdmins() && $request->user_type === 'admin';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => $request->user_type,
            'is_enrolled' => false,
            'is_approved' => $isFirstAdmin, // Auto-approve first admin
            'approved_at' => $isFirstAdmin ? now() : null,
            'approved_by' => null, // First admin doesn't need an approver
        ]);

        if ($isFirstAdmin) {
            // Auto-login the first admin
            Auth::login($user);
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome! You are now the first administrator of the music school.');
        } else {
            // Require approval for subsequent registrations
            return redirect()->route('login')
                ->with('info', 'Registration successful! Your account is pending approval from an administrator. You will receive notification once approved.');
        }
    }
}