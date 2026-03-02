<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title }} - SecureNotes</title>
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
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        h1 { 
            color: #333;
            font-size: 28px;
        }
        .meta {
            color: #999;
            font-size: 14px;
            margin-top: 5px;
        }
        .content {
            line-height: 1.8;
            color: #444;
            font-size: 16px;
            white-space: pre-wrap;
            margin-bottom: 30px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
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
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="header">
            <div>
                <h1>{{ $note->title }}</h1>
                <div class="meta">
                    Created {{ $note->created_at->diffForHumans() }}
                    @if($note->updated_at != $note->created_at)
                        · Updated {{ $note->updated_at->diffForHumans() }}
                    @endif
                </div>
            </div>
        </div>

        <div class="content">{{ $note->content }}</div>

        <div class="actions">
            <a href="{{ route('notes.edit', $note) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('notes.index') }}" class="btn btn-secondary">Back to Notes</a>
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
</body>
</html>