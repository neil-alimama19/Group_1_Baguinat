<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SPAMAST SSG</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Same dark purple background as the login page */
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); /* purple-theme shadow */
        }

        .brand { text-align: center; margin-bottom: 28px; }
        .brand h2 { color: #6d28d9; font-size: 26px; } /* purple brand title */
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
        /* Purple focus ring, same as login */
        input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
        }

        /* Vibrant purple button, same as login */
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
        button:hover { background: #6d28d9; } /* darker purple on hover */

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .error ul { margin-left: 18px; }

        .switch { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
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

        <h1>Student Registration</h1>
        <p class="sub">Create your student account to join campus events.</p>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}" placeholder="e.g. 2024-00123" required>

            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Juan Dela Cruz" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>

            <label for="course">Course</label>
            <input type="text" id="course" name="course" value="{{ old('course') }}" placeholder="e.g. BSIT">

            <label for="year_level">Year Level</label>
            <input type="text" id="year_level" name="year_level" value="{{ old('year_level') }}" placeholder="e.g. 1st Year">

            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09xxxxxxxxx">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>

            <button type="submit">Register</button>
        </form>

        <p class="switch">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        <a class="back" href="{{ route('landing') }}">&larr; Back to home</a>
    </div>
</body>
</html>