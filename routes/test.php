<?php

// Test route to debug admin access
Route::get('/test-admin', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    $user = auth()->user();
    return response()->json([
        'authenticated' => true,
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'user_type' => $user->user_type,
        'is_admin' => $user->isAdmin(),
    ]);
});