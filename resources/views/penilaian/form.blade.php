@extends('layouts.app')
@section('title', 'Penilaian: '.$alternatif->kode.' - '.$alternatif->nama)
@section('content')
<div class="card" style="max-width:820px">
    <div class="card-body">
        @if ($kriteria->isEmpty())
            <p class="text-secondary mb-0">Belum ada kriteria.</p>
        @else
        <form method="POST" action="{{ route('penilaian.update', $alternatif) }}">
            @csrf @method('PUT')
            <table class="table align-middle">
                <thead class="table-light"><tr><th style="width:35%">Kriteria</th><th>Nilai</th></tr></thead>
                <tbody>
                @foreach ($kriteria as $k)
                    @php $current = old("nilai.{$k->id}", $k->subKriteria->isNotEmpty() ? $nilai[$k->id]->sub_kriteria_id ?? null : $nilai[$k->id]->nilai ?? null); @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $k->kode }} – {{ $k->nama }}</div>
                            <small class="text-secondary">{{ ucfirst($k->jenis) }} · bobot {{ $k->bobot }}</small>
                        </td>
                        <td>
                            @if ($k->subKriteria->isNotEmpty())
                                <select name="nilai[{{ $k->id }}]" class="form-select @error("nilai.{$k->id}") is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($k->subKriteria as $s)
                                        <option value="{{ $s->id }}" @selected((string) $current === (string) $s->id)>{{ $s->nama }} (nilai {{ rtrim(rtrim(number_format($s->nilai, 2), '0'), '.') }})</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="number" step="any" name="nilai[{{ $k->id }}]" value="{{ $current !== null ? rtrim(rtrim(number_format((float) $current, 2, '.', ''), '0'), '.') : '' }}" class="form-control @error("nilai.{$k->id}") is-invalid @enderror" required>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Penilaian</button>
            <a href="{{ route('penilaian.index') }}" class="btn btn-light">Batal</a>
        </form>
        @endif
    </div>
</div>
@endsection
