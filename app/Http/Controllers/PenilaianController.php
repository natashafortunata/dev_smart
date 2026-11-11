<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenilaianController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $alternatif = Alternatif::with('penilaian.subKriteria')->orderBy('kode')->get();

        return view('penilaian.index', compact('kriteria', 'alternatif'));
    }

    public function edit(Alternatif $alternatif)
    {
        $kriteria = Kriteria::with('subKriteria')->orderBy('kode')->get();
        $nilai = $alternatif->penilaian()->get()->keyBy('kriteria_id');

        return view('penilaian.form', compact('alternatif', 'kriteria', 'nilai'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $kriteria = Kriteria::with('subKriteria')->get();
        $input = $request->input('nilai', []);
        $errors = [];
        $rows = [];

        foreach ($kriteria as $k) {
            $v = $input[$k->id] ?? null;
            if ($v === null || $v === '') {
                $errors["nilai.{$k->id}"] = "Nilai untuk {$k->kode} - {$k->nama} wajib diisi.";

                continue;
            }

            if ($k->subKriteria->isNotEmpty()) {
                $sub = SubKriteria::where('kriteria_id', $k->id)->find($v);
                if (! $sub) {
                    $errors["nilai.{$k->id}"] = "Pilihan untuk {$k->kode} tidak valid.";

                    continue;
                }
                $rows[$k->id] = ['sub_kriteria_id' => $sub->id, 'nilai' => $sub->nilai];
            } else {
                if (! is_numeric($v)) {
                    $errors["nilai.{$k->id}"] = "Nilai untuk {$k->kode} harus berupa angka.";

                    continue;
                }
                $rows[$k->id] = ['sub_kriteria_id' => null, 'nilai' => (float) $v];
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($rows, $alternatif) {
            foreach ($rows as $kriteriaId => $row) {
                Penilaian::updateOrCreate(
                    ['alternatif_id' => $alternatif->id, 'kriteria_id' => $kriteriaId],
                    $row
                );
            }
        });

        return redirect()->route('penilaian.index')->with('success', "Penilaian {$alternatif->nama} berhasil disimpan.");
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->penilaian()->delete();

        return redirect()->route('penilaian.index')->with('success', "Penilaian {$alternatif->nama} berhasil dikosongkan.");
    }
}
