<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPAMAST SSG</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        /* 1. Updated Background Gradient (Dark Purple) */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #2e1065, #1e1b4b); /* Deep purple to dark indigo */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e293b;
        }

        .card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
            border-radius: 20px;
            /* 2. Updated Card Shadow to match purple theme */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
        }

        .brand { text-align: center; margin-bottom: 28px; }
        
        /* 3. Updated Brand Title Color */
        .brand h2 { color: #6d28d9; font-size: 26px; }
        
        .brand span {
            display: block;
            font-size: 11px;
            letter-spacing: 3px;
            color: #64748b;
            margin-top: 4px;
        }

        h1 { font-size: 22px; color: #1e1b4b; margin-bottom: 6px; }
        .sub { color: #64748b; font-size: 14px; margin-bottom: 24px; }
        
        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e1b4b; }
        
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            margin-bottom: 18px;
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        /* 4. Updated Input Focus Ring to purple */
        input:focus { 
            border-color: #8b5cf6; 
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25); 
        }

        /* 5. Updated Button Color (Vibrant Purple) */
        button {
            width: 100%;
            padding: 13px;
            background: #7c3aed; 
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        button:hover { background: #6d28d9; } /* Darker purple on hover */
        
        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        
        .success {
            background: #dcfce7;
            color: #166534;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }
        .remember input {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        .switch { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
        
        /* 6. Updated Links to match theme */
        .switch a { color: #7c3aed; font-weight: 600; text-decoration: none; transition: color 0.2s; }
        .switch a:hover { color: #6d28d9; }
        
        .back { display: block; text-align: center; margin-top: 14px; font-size: 13px; color: #64748b; text-decoration: none; transition: color 0.2s; }
        .back:hover { color: #1e1b4b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <h2>SPAMAST SSG</h2>
            <span>EVENT MANAGEMENT SYSTEM</span>
        </div>

        <h1>Welcome back</h1>
        <p class="sub">Log in to your account to continue.</p>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="error">{{ session('error') }}</div>
        @elseif ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <label class="remember" for="remember">
                <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>

            <button type="submit">Login</button>
        </form>

        <p class="switch">No account yet? <a href="{{ route('register') }}">Register here</a></p>
        <a class="back" href="{{ url('/') }}">&larr; Back to home</a>
    </div>
</body>
</html>