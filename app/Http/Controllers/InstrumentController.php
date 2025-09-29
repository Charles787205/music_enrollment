<?php

namespace App\Http\Controllers;

use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstrumentController extends Controller
{
    /**
     * Display a listing of available instruments.
     */
    public function index(Request $request)
    {
        $query = Instrument::available();
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }
        
        // Filter by difficulty if provided
        if ($request->has('difficulty') && $request->difficulty) {
            $query->byDifficulty($request->difficulty);
        }
        
        // Search by name if provided
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $instruments = $query->orderBy('category')->orderBy('name')->get();
        
        // Get unique categories and difficulties for filters
        $categories = Instrument::distinct()->pluck('category');
        $difficulties = Instrument::distinct()->pluck('difficulty_level');
        
        return view('instruments.index', compact('instruments', 'categories', 'difficulties'));
    }

    /**
     * Show the form for creating a new instrument (admin only).
     */
    public function create()
    {
        \Log::info('InstrumentController create method called');
        return view('instruments.create');
    }

    /**
     * Store a newly created instrument in storage (admin only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:instruments',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:string,wind,brass,percussion,keyboard',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'rental_fee' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $instrument = Instrument::create($request->all());

        return redirect()->route('instruments.show', $instrument)
            ->with('success', 'Instrument created successfully!');
    }

    /**
     * Display the specified instrument.
     */
    public function show(Instrument $instrument)
    {
        $borrowCount = $instrument->instrumentBorrows()
            ->whereIn('status', ['pending', 'borrowed'])
            ->count();
        
        return view('instruments.show', compact('instrument', 'borrowCount'));
    }

    /**
     * Show the form for editing the specified instrument (admin only).
     */
    public function edit(Instrument $instrument)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('instruments.edit', compact('instrument'));
    }

    /**
     * Update the specified instrument in storage (admin only).
     */
    public function update(Request $request, Instrument $instrument)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name,' . $instrument->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:string,wind,brass,percussion,keyboard',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'rental_fee' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $instrument->update($request->all());

        return redirect()->route('instruments.show', $instrument)
            ->with('success', 'Instrument updated successfully!');
    }

    /**
     * Remove the specified instrument from storage (admin only).
     */
    public function destroy(Instrument $instrument)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // Check if instrument has active enrollments
        if ($instrument->enrollments()->active()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete instrument with active enrollments.');
        }

        $instrument->delete();

        return redirect()->route('instruments.index')
            ->with('success', 'Instrument deleted successfully!');
    }

    /**
     * Admin dashboard for instruments
     */
    public function admin()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $instruments = Instrument::withCount(['enrollments' => function($query) {
            $query->active();
        }])->orderBy('name')->get();
        
        return view('admin.instruments.index', compact('instruments'));
    }

    /**
     * Toggle instrument availability
     */
    public function toggleAvailability(Instrument $instrument)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $instrument->update(['is_available' => !$instrument->is_available]);
        
        $status = $instrument->is_available ? 'available' : 'unavailable';
        
        return redirect()->back()
            ->with('success', "Instrument marked as {$status}.");
    }
}
