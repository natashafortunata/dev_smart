<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function store(Request $request, Kriteria $kriteria)
    {
        $kriteria->subKriteria()->create($this->validasi($request));

        return redirect()->route('kriteria.show', $kriteria)->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, SubKriteria $subKriteria)
    {
        $data = $this->validasi($request);
        $subKriteria->update($data);

        // Sinkronkan nilai penilaian yang memakai sub kriteria ini
        Penilaian::where('sub_kriteria_id', $subKriteria->id)->update(['nilai' => $data['nilai']]);

        return redirect()->route('kriteria.show', $subKriteria->kriteria_id)->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    public function destroy(SubKriteria $subKriteria)
    {
        $kriteriaId = $subKriteria->kriteria_id;
        $subKriteria->delete();

        return redirect()->route('kriteria.show', $kriteriaId)->with('success', 'Sub kriteria berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nilai' => ['required', 'numeric'],
        ]);
    }
}
