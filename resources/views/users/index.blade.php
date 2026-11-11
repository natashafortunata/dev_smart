@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Daftar Pengguna</span>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Pengguna</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>Role</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $u->name }}</td><td>{{ $u->email }}</td>
                    <td><span class="badge {{ $u->role === 'admin' ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ ucfirst($u->role) }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        @unless ($u->is(auth()->user()))
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" data-confirm="Hapus pengguna {{ $u->name }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
