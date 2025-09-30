<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the password change form.
     */
    public function show()
    {
        return view('auth.change-password');
    }
    
    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        // Simple validation with custom messages
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'password.min' => 'The new password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])
                        ->withInput();
        }

        try {
            // Update password and clear the password change requirement
            $user->password = Hash::make($request->password);
            $user->password_change_required = false;
            $saved = $user->save();

            if ($saved) {
                // Redirect back to password change page with success message
                return redirect()->route('password.change')
                    ->with('success', 'Password changed successfully! You can now continue using your account.')
                    ->with('password_changed', true);
            } else {
                return back()->withErrors(['general' => 'Failed to save password changes.'])
                            ->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'An error occurred: ' . $e->getMessage()])
                        ->withInput();
        }
    }
}
