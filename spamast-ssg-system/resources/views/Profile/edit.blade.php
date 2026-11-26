<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - SPAMAST SSG</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #2e1065, #1e1b4b);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            color: #1e293b;
        }

        .card {
            background: #ffffff;
            width: 100%;
            max-width: 600px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
        }

        h1 { font-size: 24px; color: #1e1b4b; margin-bottom: 6px; }
        .sub { color: #64748b; font-size: 14px; margin-bottom: 24px; }
        
        .row { display: flex; gap: 16px; margin-bottom: 16px; }
        .col { flex: 1; }
        
        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e1b4b; }
        
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        input:focus { 
            border-color: #8b5cf6; 
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25); 
        }

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
            margin-top: 10px;
        }

        button:hover { background: #6d28d9; }
        
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

        .back { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; text-decoration: none; }
        .back:hover { color: #1e1b4b; }

        hr { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        .section-title { font-size: 16px; font-weight: bold; color: #1e1b4b; margin-bottom: 16px; }

        /* Profile photo preview */
        .photo-row { align-items: center; }
        .photo-preview {
            width: 64px; height: 64px;
            border-radius: 50%;
            object-fit: cover;          /* crop the image to fill the circle */
            flex-shrink: 0;
        }
        .photo-initials {
            background: #7c3aed; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 22px;
        }
        .hint { display: block; font-size: 12px; color: #64748b; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Edit Profile</h1>
        <p class="sub">Update your account information and password.</p>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="error">
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- enctype is required, otherwise the file is not sent to the server --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row photo-row">
                {{-- Current photo, or initials when there is no photo --}}
                @if ($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile photo" class="photo-preview">
                @else
                    <div class="photo-preview photo-initials">{{ substr($user->first_name ?? $user->name, 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}</div>
                @endif
                <div class="col">
                    <label for="photo">Profile Photo (Optional)</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                    <span class="hint">JPG, JPEG, PNG or WEBP. Maximum 2 MB.</span>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                </div>
                <div class="col">
                    <label for="middle_name">Middle Name (Optional)</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <hr>
            <div class="section-title">Change Password (Leave blank to keep current)</div>

            <div class="row">
                <div class="col">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="col">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <button type="submit">Save Changes</button>
        </form>

                {{-- "main" sends the logged-in user to their own role dashboard --}}
        <a href="{{ route('main') }}" class="back">&larr; Back to Dashboard</a>
    </div>
</body>
</html>