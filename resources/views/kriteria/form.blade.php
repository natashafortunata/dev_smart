@extends('layouts.app')
@section('title', $kriteria->exists ? 'Edit Kriteria' : 'Tambah Kriteria')
@section('content')
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ $kriteria->exists ? route('kriteria.update', $kriteria) : route('kriteria.store') }}">
            @csrf
            @if ($kriteria->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kode</label>
                    <input name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode', $kriteria->kode) }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Nama Kriteria</label>
                    <input name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $kriteria->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bobot (1–100)</label>
                    <input type="number" step="0.01" min="0.01" max="100" name="bobot" class="form-control @error('bobot') is-invalid @enderror" value="{{ old('bobot', $kriteria->bobot) }}" required>
                    <div class="form-text">Bobot dinormalisasi otomatis sehingga total = 1.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select">
                        @foreach (['benefit' => 'Benefit (lebih besar lebih baik)', 'cost' => 'Cost (lebih kecil lebih baik)'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('jenis', $kriteria->jenis) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $kriteria->keterangan) }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
