<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlternatifController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->query('cari');
        $alternatif = Alternatif::query()
            ->when($cari, fn ($q) => $q->where(fn ($w) => $w
                ->where('nama', 'ilike', "%{$cari}%")
                ->orWhere('kode', 'ilike', "%{$cari}%")))
            ->withCount('penilaian')
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('alternatif.index', compact('alternatif', 'cari'));
    }

    public function create()
    {
        $angka = Alternatif::pluck('kode')->map(fn ($k) => (int) preg_replace('/\D/', '', $k))->max() ?? 0;

        return view('alternatif.form', ['alternatif' => new Alternatif(['kode' => 'A'.($angka + 1)])]);
    }

    public function store(Request $request)
    {
        Alternatif::create($this->validasi($request));

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan.');
    }

    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.form', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $alternatif->update($this->validasi($request, $alternatif));

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diperbarui.');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus.');
    }

    private function validasi(Request $request, ?Alternatif $alternatif = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('alternatif', 'kode')->ignore($alternatif?->id)],
            'nama' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
        ]);
    }
}
