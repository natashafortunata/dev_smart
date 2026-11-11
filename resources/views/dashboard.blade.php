@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Kriteria', $jumlahKriteria, 'bi-list-check', 'primary'],
            ['Alternatif', $jumlahAlternatif, 'bi-people', 'success'],
            ['Penilaian Terisi', $jumlahPenilaian.' / '.$targetPenilaian, 'bi-pencil-square', 'warning'],
            ['Hasil Tersimpan', $topHasil->count() ? 'Ada' : 'Belum', 'bi-trophy', 'danger'],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $icon, $color])
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100"><div class="card-body d-flex align-items-center">
                <div class="rounded-3 bg-{{ $color }}-subtle text-{{ $color }} p-3 me-3"><i class="bi {{ $icon }} fs-4"></i></div>
                <div><div class="text-secondary small">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-trophy me-1"></i>5 Peringkat Teratas</div>
            <div class="card-body">
                @if ($topHasil->isEmpty())
                    <p class="text-secondary mb-0">Belum ada hasil tersimpan. Lakukan penilaian lalu buka menu <a href="{{ route('perhitungan.index') }}">Perhitungan</a>.</p>
                @else
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Peringkat</th><th>Kode</th><th>Alternatif</th><th class="text-end">Nilai Akhir</th></tr></thead>
                        <tbody>
                        @foreach ($topHasil as $h)
                            <tr>
                                <td><span class="badge {{ $h->peringkat == 1 ? 'text-bg-warning' : 'text-bg-light' }}">#{{ $h->peringkat }}</span></td>
                                <td>{{ $h->alternatif->kode }}</td>
                                <td>{{ $h->alternatif->nama }}</td>
                                <td class="text-end fw-semibold">{{ number_format($h->nilai_akhir, 4) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-info-circle me-1"></i>Tentang Metode SMART</div>
            <div class="card-body small">
                <p><strong>SMART</strong> (Simple Multi Attribute Rating Technique) adalah metode pengambilan keputusan multi-kriteria yang dikembangkan oleh Edward (1977).</p>
                <ol class="ps-3 mb-3">
                    <li>Menentukan kriteria dan bobotnya.</li>
                    <li>Normalisasi bobot: <code>w<sub>j</sub> = bobot<sub>j</sub> / Σbobot</code></li>
                    <li>Menghitung nilai utility tiap kriteria.</li>
                    <li>Nilai akhir: <code>V<sub>i</sub> = Σ w<sub>j</sub> × u<sub>ij</sub></code></li>
                    <li>Perankingan dari nilai terbesar.</li>
                </ol>
                @if ($kriteria->isNotEmpty())
                    <div class="fw-semibold mb-1">Kriteria aktif:</div>
                    @foreach ($kriteria as $k)
                        <span class="badge text-bg-light border me-1 mb-1">{{ $k->kode }} {{ $k->nama }} ({{ $k->jenis }})</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
