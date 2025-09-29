<?php

namespace App\Http\Controllers;

use App\Models\InstrumentBorrow;
use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstrumentBorrowController extends Controller
{
    /**
     * Display a listing of the user's instrument borrows.
     */
    public function index()
    {
        $borrows = InstrumentBorrow::where('user_id', Auth::id())
            ->with('instrument')
            ->latest()
            ->paginate(10);

        return view('instrument_borrows.index', compact('borrows'));
    }

    /**
     * Show the form for creating a new borrow request.
     */
    public function create(Request $request)
    {
        $instruments = Instrument::where('is_available', true)->get();
        $selectedInstrument = null;
        
        if ($request->has('instrument')) {
            $selectedInstrument = Instrument::find($request->get('instrument'));
        }
        
        return view('instrument_borrows.create', compact('instruments', 'selectedInstrument'));
    }

    /**
     * Store a newly created borrow request in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instrument_id' => 'required|exists:instruments,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if instrument is still available
        $instrument = Instrument::findOrFail($request->instrument_id);
        if (!$instrument->is_available) {
            return redirect()->back()
                ->withErrors(['instrument_id' => 'This instrument is no longer available.'])
                ->withInput();
        }

        // Check if user already has a pending or active borrow for this instrument
        $existingBorrow = InstrumentBorrow::where('user_id', Auth::id())
            ->where('instrument_id', $request->instrument_id)
            ->whereIn('status', ['pending', 'borrowed'])
            ->first();

        if ($existingBorrow) {
            return redirect()->back()
                ->withErrors(['instrument_id' => 'You already have a pending or active borrow for this instrument.'])
                ->withInput();
        }

        InstrumentBorrow::create([
            'user_id' => Auth::id(),
            'instrument_id' => $request->instrument_id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('instrument-borrows.index')
            ->with('success', 'Instrument borrow request submitted successfully! Please wait for approval.');
    }

    /**
     * Display the specified borrow request.
     */
    public function show(InstrumentBorrow $instrumentBorrow)
    {
        // Check if the authenticated user owns this borrow request
        if ($instrumentBorrow->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this borrow request.');
        }

        $instrumentBorrow->load('instrument');
        return view('instrument_borrows.show', compact('instrumentBorrow'));
    }

    /**
     * Show the form for editing the specified borrow request.
     */
    public function edit(InstrumentBorrow $instrumentBorrow)
    {
        // Check if the authenticated user owns this borrow request
        if ($instrumentBorrow->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this borrow request.');
        }

        // Only allow editing if status is pending
        if ($instrumentBorrow->status !== 'pending') {
            return redirect()->route('instrument-borrows.show', $instrumentBorrow)
                ->withErrors(['status' => 'You can only edit pending borrow requests.']);
        }

        $instruments = Instrument::where('is_available', true)
            ->orWhere('id', $instrumentBorrow->instrument_id)
            ->get();

        return view('instrument_borrows.edit', compact('instrumentBorrow', 'instruments'));
    }

    /**
     * Update the specified borrow request in storage.
     */
    public function update(Request $request, InstrumentBorrow $instrumentBorrow)
    {
        // Check if the authenticated user owns this borrow request
        if ($instrumentBorrow->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this borrow request.');
        }

        // Only allow editing if status is pending
        if ($instrumentBorrow->status !== 'pending') {
            return redirect()->route('instrument-borrows.show', $instrumentBorrow)
                ->withErrors(['status' => 'You can only edit pending borrow requests.']);
        }

        $validator = Validator::make($request->all(), [
            'instrument_id' => 'required|exists:instruments,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if instrument is still available (if changing instrument)
        if ($request->instrument_id != $instrumentBorrow->instrument_id) {
            $instrument = Instrument::findOrFail($request->instrument_id);
            if (!$instrument->is_available) {
                return redirect()->back()
                    ->withErrors(['instrument_id' => 'This instrument is no longer available.'])
                    ->withInput();
            }
        }

        $instrumentBorrow->update([
            'instrument_id' => $request->instrument_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('instrument-borrows.show', $instrumentBorrow)
            ->with('success', 'Borrow request updated successfully!');
    }

    /**
     * Remove the specified borrow request from storage.
     */
    public function destroy(InstrumentBorrow $instrumentBorrow)
    {
        // Check if the authenticated user owns this borrow request
        if ($instrumentBorrow->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this borrow request.');
        }

        // Only allow deletion if status is pending
        if ($instrumentBorrow->status !== 'pending') {
            return redirect()->route('instrument-borrows.index')
                ->withErrors(['status' => 'You can only cancel pending borrow requests.']);
        }

        $instrumentBorrow->delete();

        return redirect()->route('instrument-borrows.index')
            ->with('success', 'Borrow request cancelled successfully!');
    }
}
