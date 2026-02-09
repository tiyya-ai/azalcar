@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Two-Factor Enrollment</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <p>Click below to enable or disable two-factor authentication for your admin account.</p>

    <form method="POST" action="{{ route('admin.2fa.enable') }}">
        @csrf
        <button class="btn btn-primary">Enable 2FA</button>
    </form>

    <form method="POST" action="{{ route('admin.2fa.disable') }}" style="margin-top: 10px;">
        @csrf
        <button class="btn btn-secondary">Disable 2FA</button>
    </form>

    <p style="margin-top:20px; color: #666;">Note: This is a minimal enrollment flow. For production, integrate with an authenticator app and secret provisioning.</p>
</div>
@endsection
