@extends('layouts.app')

@section('title', 'Create Institute')

@section('content')
    <h1 style="font-size: 28px; color: #c084fc; margin-bottom: 20px;">Create Institute</h1>

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

        <form method="POST" action="{{ route('admin.institutes.store') }}">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: none;" required>
            </div>

            <button type="submit" style="background: #7c3aed; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer;">Save Institute</button>
            <a href="{{ route('admin.institutes.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none;">Cancel</a>
        </form>
    </div>
@endsection