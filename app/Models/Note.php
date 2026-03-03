<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];


    /**
     * A note belongs to a user
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }


    /**
     * All shares of this note
     */
    public function shares(): HasMany
    {
        return $this->hasMany(NoteShare::class);
    }


    /**
     * Users who have access to this note (via shares)
     */
    public function sharedWith(): BelongsToMany 
    {
        return $this->belongsToMany(User::class, 'note_shares')
            ->withPivot('permission')
            ->withTimestamps();
    }


    /**
     * Check if a user can view this note
     */
    public function canView(User $user): bool
    {
        // Owner can always view
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check if shared with read or write permission
        return $this->shares()
            ->where('user_id', $user->id)
            ->exists();
    }


    /**
     * Check if a user can edit this note
     */
    public function canEdit(User $user): bool
    {
        // Owner can always edit
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check if shared with write permission
        return $this->shares()
            ->where('user_id', $user->id)
            ->where('permission', 'write')
            ->exists();
    }
}
