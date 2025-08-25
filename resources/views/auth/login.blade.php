<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Login</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .logo {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #4f46e5;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
        }

        .error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">Awaz SuperAdmin</div>

        @if(session('errors'))
            <div class="error" style="margin-bottom: 1rem;">
                {{ session('errors')->first('credentials') }}
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
