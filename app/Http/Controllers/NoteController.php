<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
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
        $user = Auth::user();

        // Get owned notes
        $ownedNotes = $user->notes()->latest()->get();

        // Get shared notes
        $sharedNotes = $user->sharedNotes()->latest()->get();

        return view('notes.index', compact('ownedNotes', 'sharedNotes'));
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
            'content' => 'required|string',
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
        // AUTHORIZATION: Can user view this note?
        if (! $note->canView(Auth::user())) {
            abort(403, 'You do not have permission to view this note.');
        }

        // Get permission level
        $permission = $this->getUserPermission($note, Auth::user());

        return view('notes.show', compact('note', 'permission'));
    }

    /**
     * Show the form for editing a note
     */
    public function edit(Note $note)
    {
        // AUTHORIZATION: Can user edit this note?
        if (! $note->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to edit this note.');
        }

        return view('notes.edit', compact('note'));
    }

    /**
     * Update a note
     */
    public function update(Request $request, Note $note)
    {
        // AUTHORIZATION: Can user edit this note?
        if (! $note->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to edit this note.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note->update($validated);

        return redirect()->route('notes.show', $note)->with('success', 'Note updated successfully!');
    }

    /**
     * Delete a note
     */
    public function destroy(Note $note)
    {
        // AUTHORIZATION: Only owner can delete
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this note.');
        }

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully!');
    }

    /**
     * Show share form
     */
    public function share(Note $note)
    {

        // AUTHORIZATION: Only owner can share
        if ( $note->user_id !== Auth::id()) {
            abort(403, 'Only the note owner can share it');
        }

        // Get all users except owner and already shared users
        $sharedUserIds = $note->shares()->pluck('user_id')->toArray();
        $availableUsers = User::where('id', '!=', Auth::id())->whereNotIn('id', $sharedUserIds)->get();

        // Get current shares
        $shares = $note->shares()->with('user')->get();

        return view('notes.share', compact('note', 'availableUsers', 'shares'));
    }

    /**
     * Store share
     */
    public function storeShare(Request $request, Note $note)
    {
        // AUTHORIZATION: Only owner can share
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Only the note owner can share it.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:read,write',
        ]);

        // Prevent sharing with self
        if ($validated['user_id'] == Auth::id()) {
            return back()->withErrors(['user_id' => 'You cannot share with yourself.']);
        }

        // Create share
        $note->shares()->create($validated);

        return back()->with('success', 'Note shared successfully!');
    }

    /**
     * Remove share
     */
    public function removeShare(Note $note, $shareId)
    {
        // AUTHORIZATION: Only owner can remove shares
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Only the note owner can manage shares.');
        }

        $note->shares()->where('id', $shareId)->delete();

        return back()->with('success', 'Share removed successfully!');
    }

    /**
     * Get user's permission level for a note
     */
    private function getUserPermission(Note $note, User $user): string
    {

        // Owner has full permission
        if ($note->user_id === $user->id) {
            return 'owner';
        }

        // Check shared permission
        $share = $note->shares()->where('user_id', $user->id)->first();

        return $share ? $share->permission : 'none';
    }
}
