<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\note;

class NoteController extends Controller
{
    /**
     * Display listing of the user's notes
     */
    public function index()
    {
        // AUTHORIZATION CHECK: only show notes belonging to authenticated user
        $notes = Auth::user()->notes()->latest()->get();
        
        return view('notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new note
     */
    public function create()
    {
        return view('notes.create');
    }

    /**
     * Store a newly created note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'reqired|string',
        ]);

        // AUTHORIZATION: Automatically set user_id to authenticate user
        // User can ONLY create notes for themselves
        $note = Auth::user()->notes()->create($validated);

        return redirect()->route('notes.index')->with('success', 'Note created successfully!');
    }

    /**
     * Display a specific note
     */
    public function show(Note $note)
    {
        // AUTHORIZATION CHECK: Does this note belong to the authenticated user?
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this note.');
        }

        return view('notes.show', compact('note'));
    }

    /**
     * Show the form for editing a note
     */
    public function edit(Note $note)
    {
        // AUTHORIZATION CHECK: Can only edit your own notes
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this note.');
        }

        return view('notes.edit', compact('note'));
    }

    /**
     * Update a note
     */
    public function update(Request $request, Note $note)
    {
        // AUTHORIZATION CHECK: Can only update your own notes
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this note.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note->update($validated);

        return redirect()->route('notes.show',$note)->with('success','Note updated successfully!');
    }

    /**
     * Delete a note
     */
    public function destroy(Note $note){
        // AUTHORIZATION CHECK: can only delete your own notes
        if($note->user_id!==Auth::id()){
            abort(403,'Unauthorized access to this note.');
        }

        $note->delete();

        
    }
}
