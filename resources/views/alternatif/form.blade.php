@extends('layouts.app')
@section('title', $alternatif->exists ? 'Edit Alternatif' : 'Tambah Alternatif')
@section('content')
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ $alternatif->exists ? route('alternatif.update', $alternatif) : route('alternatif.store') }}">
            @csrf
            @if ($alternatif->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kode</label>
                    <input name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode', $alternatif->kode) }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Nama Alternatif</label>
                    <input name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $alternatif->nama) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $alternatif->keterangan) }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('alternatif.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
