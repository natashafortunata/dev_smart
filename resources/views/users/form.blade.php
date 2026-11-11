@extends('layouts.app')
@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
            <div class="mb-3"><label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin (kelola semua data)</option>
                    <option value="pimpinan" @selected(old('role', $user->role) === 'pimpinan')>Pimpinan (lihat perhitungan & hasil)</option>
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" @unless($user->exists) required @endunless></div>
                <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                @if ($user->exists)<div class="form-text mt-1">Kosongkan jika tidak ingin mengganti password.</div>@endif
            </div>
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
        </form>
    </div>
</div>
@endsection
