<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SecureNotes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            background: #f5f5f5;
            padding: 40px;
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
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        h1 { color: #333; }
        .user-info { color: #666; margin-top: 5px; }
        .actions {
            display: flex;
            gap: 10px;
        }
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
            background: #d32f2f;
            color: white;
        }
        .card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .card p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Welcome, {{ auth()->user()->name }}!</h1>
                <p class="user-info">{{ auth()->user()->email }}</p>
            </div>
            <div class="actions">
                <a href="{{ route('notes.index') }}" class="btn btn-primary">My Notes</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <h3>📝 Your Notes</h3>
            <p>Create, edit, and manage your secure notes. All notes are private and only accessible by you.</p>
            <br>
            <a href="{{ route('notes.index') }}" class="btn btn-primary">View All Notes</a>
        </div>

        <div class="card">
            <h3>🔐 Security</h3>
            <p>Your account is protected with password hashing, session fingerprinting, and CSRF protection.</p>
        </div>
    </div>
</body>
</html>