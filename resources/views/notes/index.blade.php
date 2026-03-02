<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes - SecureNotes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            background: #f5f5f5;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-info { color: #666; font-size: 14px; }
        .actions { display: flex; gap: 10px; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
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
            padding: 5px 10px;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .note-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .note-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .note-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .note-content {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
            max-height: 100px;
            overflow: hidden;
        }
        .note-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .note-date {
            font-size: 12px;
            color: #999;
        }
        .note-actions {
            display: flex;
            gap: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }
        .empty-state h2 {
            color: #666;
            margin-bottom: 10px;
        }
        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>My Notes</h1>
            <p class="user-info">{{ auth()->user()->email }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('notes.create') }}" class="btn btn-primary">+ New Note</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($notes->isEmpty())
        <div class="empty-state">
            <h2>No notes yet</h2>
            <p>Create your first note to get started</p>
            <a href="{{ route('notes.create') }}" class="btn btn-primary">Create Note</a>
        </div>
    @else
        <div class="notes-grid">
            @foreach($notes as $note)
                <div class="note-card">
                    <div class="note-title">{{ $note->title }}</div>
                    <div class="note-content">{{ Str::limit($note->content, 150) }}</div>
                    <div class="note-footer">
                        <span class="note-date">{{ $note->created_at->diffForHumans() }}</span>
                        <div class="note-actions">
                            <a href="{{ route('notes.show', $note) }}" class="btn btn-secondary">View</a>
                            <a href="{{ route('notes.edit', $note) }}" class="btn btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('notes.destroy', $note) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this note?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>