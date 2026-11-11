@extends('layouts.app')
@section('title', 'Sub Kriteria: '.$kriteria->kode.' - '.$kriteria->nama)
@section('content')
<a href="{{ route('kriteria.index') }}" class="btn btn-light btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Tambah Sub Kriteria</div>
            <div class="card-body">
                <form method="POST" action="{{ route('sub-kriteria.store', $kriteria) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Rentang</label>
                        <input name="nama" class="form-control" placeholder="mis. Sangat Baik / > 5 tahun" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai</label>
                        <input type="number" step="0.01" name="nilai" class="form-control" placeholder="mis. 100" required>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
                </form>
            </div>
        </div>
        <div class="card mt-3"><div class="card-body small">
            <div><strong>Jenis:</strong> {{ ucfirst($kriteria->jenis) }}</div>
            <div><strong>Bobot:</strong> {{ $kriteria->bobot }}</div>
            @if ($kriteria->keterangan)<div class="mt-2 text-secondary">{{ $kriteria->keterangan }}</div>@endif
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Daftar Sub Kriteria</div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead class="table-light"><tr><th>Keterangan</th><th style="width:140px">Nilai</th><th class="text-end" style="width:150px">Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($kriteria->subKriteria as $s)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('sub-kriteria.update', $s) }}" id="upd{{ $s->id }}">@csrf @method('PUT')</form>
                                <input form="upd{{ $s->id }}" name="nama" value="{{ $s->nama }}" class="form-control form-control-sm" required>
                            </td>
                            <td><input form="upd{{ $s->id }}" type="number" step="0.01" name="nilai" value="{{ $s->nilai }}" class="form-control form-control-sm" required></td>
                            <td class="text-end text-nowrap">
                                <button form="upd{{ $s->id }}" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i></button>
                                <form method="POST" action="{{ route('sub-kriteria.destroy', $s) }}" class="d-inline" data-confirm="Hapus sub kriteria ini?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada sub kriteria. Penilaian kriteria ini akan memakai input angka langsung.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
