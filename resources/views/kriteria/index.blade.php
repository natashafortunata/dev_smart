@extends('layouts.app')
@section('title', 'Data Kriteria')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Daftar Kriteria</span>
        <a href="{{ route('kriteria.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Kriteria</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Kode</th><th>Nama Kriteria</th><th>Jenis</th><th class="text-end">Bobot</th><th class="text-end">Normalisasi (w)</th><th class="text-center">Sub Kriteria</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($kriteria as $k)
                <tr>
                    <td class="fw-semibold">{{ $k->kode }}</td>
                    <td>{{ $k->nama }}</td>
                    <td><span class="badge {{ $k->jenis === 'benefit' ? 'text-bg-success' : 'text-bg-danger' }}">{{ ucfirst($k->jenis) }}</span></td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($k->bobot, 2), '0'), '.') }}</td>
                    <td class="text-end">{{ $totalBobot > 0 ? number_format($k->bobot / $totalBobot, 4) : '-' }}</td>
                    <td class="text-center"><a href="{{ route('kriteria.show', $k) }}" class="btn btn-outline-secondary btn-sm">{{ $k->sub_kriteria_count }} item</a></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('kriteria.edit', $k) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('kriteria.destroy', $k) }}" method="POST" class="d-inline" data-confirm="Hapus kriteria {{ $k->nama }}? Data penilaian terkait ikut terhapus.">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data kriteria.</td></tr>
            @endforelse
            </tbody>
            @if ($kriteria->isNotEmpty())
                <tfoot class="table-light fw-semibold">
                    <tr><td colspan="3">Total</td><td class="text-end">{{ rtrim(rtrim(number_format($totalBobot, 2), '0'), '.') }}</td><td class="text-end">1.0000</td><td colspan="2"></td></tr>
                </tfoot>
            @endif
        </table>
        <div class="small text-secondary">
            <i class="bi bi-info-circle"></i> <strong>Benefit</strong>: semakin besar nilai semakin baik. <strong>Cost</strong>: semakin kecil nilai semakin baik.
            Kriteria tanpa sub kriteria dinilai dengan input angka langsung.
        </div>
    </div>
</div>
@endsection
