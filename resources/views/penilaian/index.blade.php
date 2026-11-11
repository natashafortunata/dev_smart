@extends('layouts.app')
@section('title', 'Penilaian Alternatif')
@section('content')
<div class="card">
    <div class="card-header bg-white fw-semibold">Matriks Penilaian (Nilai Alternatif per Kriteria)</div>
    <div class="card-body table-responsive">
        @if ($kriteria->isEmpty() || $alternatif->isEmpty())
            <p class="text-secondary mb-0">Lengkapi data <a href="{{ route('kriteria.index') }}">kriteria</a> dan <a href="{{ route('alternatif.index') }}">alternatif</a> terlebih dahulu.</p>
        @else
        <table class="table table-bordered table-hover align-middle small">
            <thead class="table-light">
                <tr>
                    <th>Kode</th><th>Alternatif</th>
                    @foreach ($kriteria as $k)<th class="text-center" title="{{ $k->nama }}">{{ $k->kode }}<div class="fw-normal text-secondary">{{ \Illuminate\Support\Str::limit($k->nama, 18) }}</div></th>@endforeach
                    <th class="text-center">Status</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($alternatif as $a)
                @php $n = $a->penilaian->keyBy('kriteria_id'); $lengkap = $kriteria->every(fn ($k) => $n->has($k->id)); @endphp
                <tr>
                    <td class="fw-semibold">{{ $a->kode }}</td>
                    <td>{{ $a->nama }}</td>
                    @foreach ($kriteria as $k)
                        <td class="text-center">
                            @if ($n->has($k->id))
                                {{ rtrim(rtrim(number_format($n[$k->id]->nilai, 2), '0'), '.') }}
                                @if ($n[$k->id]->subKriteria)<div class="text-secondary" style="font-size:.7rem">{{ $n[$k->id]->subKriteria->nama }}</div>@endif
                            @else
                                <span class="text-danger">–</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-center">{!! $lengkap ? '<span class="badge text-bg-success">Lengkap</span>' : '<span class="badge text-bg-warning">Belum</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('penilaian.edit', $a) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Nilai</a>
                        @if ($n->isNotEmpty())
                        <form method="POST" action="{{ route('penilaian.destroy', $a) }}" class="d-inline" data-confirm="Kosongkan penilaian {{ $a->nama }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-eraser"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
