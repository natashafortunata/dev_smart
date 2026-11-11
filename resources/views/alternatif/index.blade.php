@extends('layouts.app')
@section('title', 'Data Alternatif')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex gap-2" method="GET">
            <input name="cari" value="{{ $cari }}" class="form-control form-control-sm" placeholder="Cari kode / nama...">
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('alternatif.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Alternatif</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>No</th><th>Kode</th><th>Nama Alternatif</th><th>Keterangan</th><th class="text-center">Penilaian</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($alternatif as $a)
                <tr>
                    <td>{{ $alternatif->firstItem() + $loop->index }}</td>
                    <td class="fw-semibold">{{ $a->kode }}</td>
                    <td>{{ $a->nama }}</td>
                    <td class="text-secondary small">{{ \Illuminate\Support\Str::limit($a->keterangan, 60) }}</td>
                    <td class="text-center"><span class="badge text-bg-light border">{{ $a->penilaian_count }} kriteria</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('penilaian.edit', $a) }}" class="btn btn-info btn-sm" title="Nilai"><i class="bi bi-pencil-square"></i></a>
                        <a href="{{ route('alternatif.edit', $a) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('alternatif.destroy', $a) }}" method="POST" class="d-inline" data-confirm="Hapus alternatif {{ $a->nama }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data alternatif.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $alternatif->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
