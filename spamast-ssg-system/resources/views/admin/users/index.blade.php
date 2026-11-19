@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #c084fc;">Manage Users</h1>
        <a href="{{ route('admin.users.create') }}" style="background: #7c3aed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Add New User</a>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 15px; margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" style="padding: 10px; border-radius: 8px; border: none; flex: 1;">
            
            <select name="role" style="padding: 10px; border-radius: 8px; border: none;">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Filter</button>
        </form>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Role</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 12px;">{{ $user->name }}</td>
                        <td style="padding: 12px;">{{ $user->email }}</td>
                        <td style="padding: 12px;">{{ $user->role?->name ?? 'N/A' }}</td>
                        <td style="padding: 12px;">
                            <span style="padding: 5px 10px; border-radius: 20px; font-size: 12px; background: {{ $user->is_active ? '#166534' : '#991b1b' }};">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display: inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="background: transparent; border: 1px solid #c084fc; color: #c084fc; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-right: 5px;">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            <a href="{{ route('admin.users.edit', $user) }}" style="color: #fbbf24; text-decoration: none; margin-right: 5px;">Edit</a>

                            @can('delete', $user)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: transparent; border: none; color: #ef4444; cursor: pointer;">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    </div>
@endsection