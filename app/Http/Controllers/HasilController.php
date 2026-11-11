<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Kriteria;

class HasilController extends Controller
{
    public function index()
    {
        $hasil = Hasil::with('alternatif')->orderBy('peringkat')->get();
        $diperbarui = Hasil::max('updated_at');

        return view('hasil.index', compact('hasil', 'diperbarui'));
    }

    public function cetak()
    {
        $hasil = Hasil::with('alternatif')->orderBy('peringkat')->get();
        $kriteria = Kriteria::orderBy('kode')->get();

        return view('hasil.cetak', compact('hasil', 'kriteria'));
    }
}
