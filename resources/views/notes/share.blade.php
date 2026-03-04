<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Note - SecureNotes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { 
            margin-bottom: 10px; 
            color: #333;
        }
        .note-title {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        select, input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .error {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-secondary {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-danger {
            background: #d32f2f;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .shares-list {
            list-style: none;
        }
        .share-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .share-info {
            flex: 1;
        }
        .share-user {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .share-email {
            font-size: 14px;
            color: #666;
        }
        .permission-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }
        .badge-read {
            background: #E3F2FD;
            color: #1976D2;
        }
        .badge-write {
            background: #FFF3E0;
            color: #F57C00;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
            background: #f9f9f9;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Share Note</h1>
        <div class="note-title">"{{ $note->title }}"</div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        {{-- Add New Share --}}
        <div class="section">
            <h2>Share with User</h2>
            
            @if($availableUsers->isEmpty())
                <div class="empty-state">
                    <p>No users available to share with. You've already shared with all users!</p>
                </div>
            @else
                <form method="POST" action="{{ route('notes.share.store', $note) }}">
                    @csrf

                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select id="user_id" name="user_id" required>
                            <option value="">-- Choose a user --</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="permission">Permission Level</label>
                        <select id="permission" name="permission" required>
                            <option value="read">Read Only - Can view the note</option>
                            <option value="write">Read & Write - Can view and edit the note</option>
                        </select>
                        @error('permission')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Share Note</button>
                </form>
            @endif
        </div>

        {{-- Current Shares --}}
        <div class="section">
            <h2>Currently Shared With</h2>
            
            @if($shares->isEmpty())
                <div class="empty-state">
                    <p>This note is not shared with anyone yet.</p>
                </div>
            @else
                <ul class="shares-list">
                    @foreach($shares as $share)
                        <li class="share-item">
                            <div class="share-info">
                                <div class="share-user">
                                    {{ $share->user->name }}
                                    <span class="permission-badge badge-{{ $share->permission }}">
                                        {{ ucfirst($share->permission) }}
                                    </span>
                                </div>
                                <div class="share-email">{{ $share->user->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('notes.share.remove', [$note, $share->id]) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Remove access for {{ $share->user->name }}?')">
                                    Remove
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="actions">
            <a href="{{ route('notes.show', $note) }}" class="btn btn-secondary">Back to Note</a>
            <a href="{{ route('notes.index') }}" class="btn btn-secondary">Back to Notes</a>
        </div>
    </div>
</body>
</html>