<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::withCount('subKriteria')->orderBy('kode')->get();
        $totalBobot = $kriteria->sum('bobot');

        return view('kriteria.index', compact('kriteria', 'totalBobot'));
    }

    public function create()
    {
        $kodeBaru = $this->kodeBerikutnya();

        return view('kriteria.form', ['kriteria' => new Kriteria(['kode' => $kodeBaru, 'jenis' => 'benefit'])]);
    }

    public function store(Request $request)
    {
        Kriteria::create($this->validasi($request));

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function show(Kriteria $kriteria)
    {
        $kriteria->load('subKriteria');

        return view('kriteria.show', compact('kriteria'));
    }

    public function edit(Kriteria $kriteria)
    {
        return view('kriteria.form', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $kriteria->update($this->validasi($request, $kriteria));

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    private function validasi(Request $request, ?Kriteria $kriteria = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('kriteria', 'kode')->ignore($kriteria?->id)],
            'nama' => ['required', 'string', 'max:100'],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'jenis' => ['required', Rule::in(['benefit', 'cost'])],
            'keterangan' => ['nullable', 'string'],
        ]);
    }

    private function kodeBerikutnya(): string
    {
        $angka = Kriteria::pluck('kode')
            ->map(fn ($k) => (int) preg_replace('/\D/', '', $k))
            ->max() ?? 0;

        return 'C'.($angka + 1);
    }
}
