@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Create New User</h1>

    <div class="card" style="max-width: 600px;">
        @if ($errors->any())
            <div style="background: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Role</label>
                <select name="role_id" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Confirm Password</label>
                <input type="password" name="password_confirmation" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    Account is Active
                </label>
            </div>

            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer;">Create User</button>
            <a href="{{ route('admin.users.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none;">Cancel</a>
        </form>
    </div>
@endsection