@extends('layouts.app')
@section('title', 'Hasil & Ranking')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold">Hasil Perankingan Metode SMART</span>
            @if ($diperbarui)<div class="small text-secondary">Disimpan: {{ \Illuminate\Support\Carbon::parse($diperbarui)->translatedFormat('d F Y H:i') }}</div>@endif
        </div>
        @if ($hasil->isNotEmpty())
            <a href="{{ route('hasil.cetak') }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Cetak Laporan</a>
        @endif
    </div>
    <div class="card-body">
        @if ($hasil->isEmpty())
            <p class="text-secondary mb-0">Belum ada hasil tersimpan. Buka menu <a href="{{ route('perhitungan.index') }}">Perhitungan</a> lalu klik <em>Simpan Hasil Perankingan</em>.</p>
        @else
            @php $terbaik = $hasil->first(); $maks = max($hasil->max('nilai_akhir'), 0.0001); @endphp
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-award-fill fs-2 me-3"></i>
                <div>Alternatif terbaik adalah <strong>{{ $terbaik->alternatif->kode }} – {{ $terbaik->alternatif->nama }}</strong> dengan nilai akhir <strong>{{ number_format($terbaik->nilai_akhir, 4) }}</strong>.</div>
            </div>
            <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light"><tr><th>Peringkat</th><th>Kode</th><th>Alternatif</th><th style="width:35%">Grafik</th><th class="text-end">Nilai Akhir</th></tr></thead>
                <tbody>
                @foreach ($hasil as $h)
                    <tr>
                        <td><span class="badge {{ $h->peringkat == 1 ? 'text-bg-warning' : ($h->peringkat <= 3 ? 'text-bg-info' : 'text-bg-light border') }} fs-6">#{{ $h->peringkat }}</span></td>
                        <td>{{ $h->alternatif->kode }}</td>
                        <td>{{ $h->alternatif->nama }}</td>
                        <td><div class="progress" style="height:10px"><div class="progress-bar" style="width: {{ $h->nilai_akhir / $maks * 100 }}%"></div></div></td>
                        <td class="text-end fw-semibold">{{ number_format($h->nilai_akhir, 4) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>
@endsection
