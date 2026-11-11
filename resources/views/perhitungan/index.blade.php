@extends('layouts.app')
@section('title', 'Perhitungan Metode SMART')
@php
    $f = fn ($v, $d = 4) => number_format($v, $d);
    $lengkapList = $alternatif->filter(fn ($a) => ! isset($kurang[$a->id]));
@endphp
@section('content')
@if ($kriteria->isEmpty() || $alternatif->isEmpty())
    <div class="alert alert-info">Data kriteria dan alternatif belum tersedia.</div>
@else

@if (! $lengkap)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Alternatif berikut belum lengkap penilaiannya dan <strong>tidak diikutkan</strong> dalam perhitungan:
        @foreach ($kurang as $altId => $kodes)
            <span class="badge text-bg-warning">{{ $alternatif->firstWhere('id', $altId)->kode }} (kurang {{ implode(', ', $kodes) }})</span>
        @endforeach
    </div>
@endif

{{-- Langkah 1 --}}
<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Langkah 1 – Normalisasi Bobot Kriteria <small class="text-secondary fw-normal">w<sub>j</sub> = bobot<sub>j</sub> / Σ bobot</small></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm text-center align-middle mb-0">
            <thead class="table-light"><tr><th class="text-start">Kriteria</th>@foreach ($kriteria as $k)<th>{{ $k->kode }}</th>@endforeach<th>Total</th></tr></thead>
            <tbody>
                <tr><th class="text-start">Jenis</th>@foreach ($kriteria as $k)<td>{{ ucfirst($k->jenis) }}</td>@endforeach<td></td></tr>
                <tr><th class="text-start">Bobot</th>@foreach ($kriteria as $k)<td>{{ $k->bobot }}</td>@endforeach<td>{{ $totalBobot }}</td></tr>
                <tr class="table-primary"><th class="text-start">Normalisasi (w)</th>@foreach ($kriteria as $k)<td>{{ $f($normalisasiBobot[$k->id]) }}</td>@endforeach<td>{{ $f(array_sum($normalisasiBobot)) }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Langkah 2 --}}
<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Langkah 2 – Matriks Keputusan (Nilai Kriteria Alternatif)</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm text-center align-middle mb-0">
            <thead class="table-light"><tr><th class="text-start">Alternatif</th>@foreach ($kriteria as $k)<th>{{ $k->kode }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($lengkapList as $a)
                <tr><td class="text-start">{{ $a->kode }} – {{ $a->nama }}</td>@foreach ($kriteria as $k)<td>{{ $f($matriks[$a->id][$k->id], 2) }}</td>@endforeach</tr>
            @endforeach
                <tr class="table-light"><th class="text-start">C<sub>max</sub></th>@foreach ($kriteria as $k)<td>{{ $f($minMax[$k->id]['max'], 2) }}</td>@endforeach</tr>
                <tr class="table-light"><th class="text-start">C<sub>min</sub></th>@foreach ($kriteria as $k)<td>{{ $f($minMax[$k->id]['min'], 2) }}</td>@endforeach</tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Langkah 3 --}}
<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Langkah 3 – Nilai Utility
        <div class="small text-secondary fw-normal">Benefit: u = (C<sub>i</sub> − C<sub>min</sub>) / (C<sub>max</sub> − C<sub>min</sub>) &nbsp;·&nbsp; Cost: u = (C<sub>max</sub> − C<sub>i</sub>) / (C<sub>max</sub> − C<sub>min</sub>)</div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm text-center align-middle mb-0">
            <thead class="table-light"><tr><th class="text-start">Alternatif</th>@foreach ($kriteria as $k)<th>{{ $k->kode }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($lengkapList as $a)
                <tr><td class="text-start">{{ $a->kode }} – {{ $a->nama }}</td>@foreach ($kriteria as $k)<td>{{ $f($utility[$a->id][$k->id]) }}</td>@endforeach</tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Langkah 4 --}}
<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Langkah 4 – Nilai Akhir <small class="text-secondary fw-normal">V<sub>i</sub> = Σ w<sub>j</sub> × u<sub>ij</sub></small></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm text-center align-middle mb-0">
            <thead class="table-light"><tr><th class="text-start">Alternatif</th>@foreach ($kriteria as $k)<th>{{ $k->kode }}</th>@endforeach<th>Nilai Akhir (V)</th></tr></thead>
            <tbody>
            @foreach ($lengkapList as $a)
                <tr><td class="text-start">{{ $a->kode }} – {{ $a->nama }}</td>@foreach ($kriteria as $k)<td>{{ $f($nilaiTerbobot[$a->id][$k->id]) }}</td>@endforeach<td class="fw-bold table-primary">{{ $f($nilaiAkhir[$a->id]) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Langkah 5 --}}
<div class="card mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Langkah 5 – Perankingan</span>
        @if (auth()->user()->role === 'admin' && $ranking->isNotEmpty())
            <form method="POST" action="{{ route('perhitungan.simpan') }}">@csrf
                <button class="btn btn-success btn-sm"><i class="bi bi-save me-1"></i>Simpan Hasil Perankingan</button>
            </form>
        @endif
    </div>
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Peringkat</th><th>Kode</th><th>Alternatif</th><th class="text-end">Nilai Akhir</th></tr></thead>
            <tbody>
            @forelse ($ranking as $r)
                <tr class="{{ $r['peringkat'] == 1 ? 'table-warning fw-semibold' : '' }}">
                    <td>#{{ $r['peringkat'] }}</td><td>{{ $r['alternatif']->kode }}</td><td>{{ $r['alternatif']->nama }}</td><td class="text-end">{{ $f($r['nilai']) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-3">Belum ada alternatif dengan penilaian lengkap.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
