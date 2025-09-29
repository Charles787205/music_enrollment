<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('instrument')->get();
        
        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create()
    {
        $instruments = Instrument::available()->get();
        $user = Auth::user();
        
        // Get instruments the user is already enrolled in
        $enrolledInstrumentIds = $user->enrollments()
            ->whereIn('status', ['active', 'pending'])
            ->pluck('instrument_id')
            ->toArray();
        
        // Filter out already enrolled instruments
        $availableInstruments = $instruments->whereNotIn('id', $enrolledInstrumentIds);
        
        return view('enrollments.create', compact('availableInstruments'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'instrument_ids' => 'required|array|min:1',
            'instrument_ids.*' => 'exists:instruments,id',
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $user = Auth::user();
        $enrollments = [];

        DB::transaction(function () use ($request, $user, &$enrollments) {
            foreach ($request->instrument_ids as $instrumentId) {
                // Check if user is already enrolled in this instrument
                $existingEnrollment = $user->enrollments()
                    ->where('instrument_id', $instrumentId)
                    ->whereIn('status', ['active', 'pending'])
                    ->first();

                if (!$existingEnrollment) {
                    $instrument = Instrument::find($instrumentId);
                    
                    $enrollment = Enrollment::create([
                        'user_id' => $user->id,
                        'instrument_id' => $instrumentId,
                        'status' => 'pending',
                        'start_date' => $request->start_date,
                        'total_fee' => $instrument->rental_fee,
                    ]);
                    
                    $enrollments[] = $enrollment;
                }
            }

            // Update user's enrolled status if they're a student
            if ($user->isStudent()) {
                $hasActiveEnrollments = $user->enrollments()
                    ->where('status', 'active')
                    ->exists();
                    
                $user->update(['is_enrolled' => $hasActiveEnrollments]);
            }
        });

        if (count($enrollments) > 0) {
            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment request submitted successfully! You will receive confirmation once reviewed.');
        } else {
            return redirect()->back()
                ->with('error', 'You are already enrolled in the selected instruments.');
        }
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        // Ensure user can only view their own enrollments
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit(Enrollment $enrollment)
    {
        // Ensure user can only edit their own enrollments
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('enrollments.edit', compact('enrollment'));
    }

    /**
     * Update the specified enrollment in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        // Ensure user can only update their own enrollments
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $enrollment->update([
            'notes' => $request->notes,
        ]);

        return redirect()->route('enrollments.show', $enrollment)
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        // Ensure user can only delete their own enrollments
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow dropping pending or active enrollments
        if (in_array($enrollment->status, ['pending', 'active'])) {
            $enrollment->update(['status' => 'dropped']);
            
            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment dropped successfully.');
        }

        return redirect()->back()
            ->with('error', 'Cannot drop this enrollment.');
    }

    /**
     * Admin methods for managing enrollments
     */
    public function admin()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollments = Enrollment::with(['user', 'instrument'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Approve an enrollment
     */
    public function approve(Enrollment $enrollment)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollment->update(['status' => 'active']);
        
        return redirect()->back()
            ->with('success', 'Enrollment approved successfully!');
    }

    /**
     * Reject an enrollment
     */
    public function reject(Enrollment $enrollment)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollment->update(['status' => 'dropped']);
        
        return redirect()->back()
            ->with('success', 'Enrollment rejected.');
    }
}
