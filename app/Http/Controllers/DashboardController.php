<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Hasil;
use App\Models\Kriteria;
use App\Models\Penilaian;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $jumlahKriteria = Kriteria::count();
        $jumlahAlternatif = Alternatif::count();
        $jumlahPenilaian = Penilaian::count();
        $targetPenilaian = $jumlahKriteria * $jumlahAlternatif;
        $topHasil = Hasil::with('alternatif')->orderBy('peringkat')->take(5)->get();
        $kriteria = Kriteria::orderBy('kode')->get();

        return view('dashboard', compact(
            'jumlahKriteria', 'jumlahAlternatif', 'jumlahPenilaian', 'targetPenilaian', 'topHasil', 'kriteria'
        ));
    }
}
