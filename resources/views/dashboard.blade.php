<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SecureNotes</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-bottom: 30px;
        }
        .user-info { color: #666; }
        form { display: inline; }
        button {
            padding: 8px 16px;
            background: #d32f2f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Welcome, {{ auth()->user()->name }}!</h1>
            <p class="user-info">{{ auth()->user()->email }}</p>
        </div>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
    
    <p>You are logged in! This is your secure dashboard.</p>
</body>
</html>