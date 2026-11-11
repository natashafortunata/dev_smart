<?php

namespace App\Http\Controllers;

use App\Services\SmartService;

class PerhitunganController extends Controller
{
    public function index(SmartService $smart)
    {
        return view('perhitungan.index', $smart->hitung());
    }

    public function simpan(SmartService $smart)
    {
        $data = $smart->simpanHasil();

        if ($data['ranking']->isEmpty()) {
            return back()->with('error', 'Belum ada alternatif dengan penilaian lengkap.');
        }

        return redirect()->route('hasil.index')->with('success', 'Hasil perhitungan SMART berhasil disimpan.');
    }
}
