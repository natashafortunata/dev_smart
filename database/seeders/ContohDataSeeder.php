<?php

namespace Database\Seeders;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Database\Seeder;

/**
 * Data CONTOH agar sistem dapat langsung dicoba.
 * Sesuaikan kriteria, bobot, sub kriteria dan alternatif dengan
 * dokumen analisis sistem Anda (atau ubah lewat menu aplikasi).
 */
class ContohDataSeeder extends Seeder
{
    public function run(): void
    {
        $kriteria = [
            ['C1', 'Kualitas Kerja', 30, 'benefit', [['Sangat Baik', 100], ['Baik', 75], ['Cukup', 50], ['Kurang', 25]]],
            ['C2', 'Kedisiplinan', 25, 'benefit', [['Sangat Baik', 100], ['Baik', 75], ['Cukup', 50], ['Kurang', 25]]],
            ['C3', 'Masa Kerja (tahun)', 15, 'benefit', []],
            ['C4', 'Jumlah Absensi (hari)', 20, 'cost', []],
            ['C5', 'Kerja Sama Tim', 10, 'benefit', [['Sangat Baik', 100], ['Baik', 75], ['Cukup', 50], ['Kurang', 25]]],
        ];

        $map = [];
        foreach ($kriteria as [$kode, $nama, $bobot, $jenis, $subs]) {
            $k = Kriteria::updateOrCreate(['kode' => $kode], compact('nama', 'bobot', 'jenis'));
            foreach ($subs as [$sNama, $sNilai]) {
                $k->subKriteria()->updateOrCreate(['nama' => $sNama], ['nilai' => $sNilai]);
            }
            $map[$kode] = $k->load('subKriteria');
        }

        // [kode, nama, C1, C2, C3, C4, C5] — sub kriteria pakai nama, lainnya angka
        $alternatif = [
            ['A1', 'Andi Pratama', 'Sangat Baik', 'Baik', 5, 2, 'Baik'],
            ['A2', 'Budi Santoso', 'Baik', 'Sangat Baik', 3, 1, 'Sangat Baik'],
            ['A3', 'Citra Lestari', 'Cukup', 'Baik', 8, 4, 'Baik'],
            ['A4', 'Dewi Anggraini', 'Sangat Baik', 'Cukup', 2, 0, 'Cukup'],
            ['A5', 'Eko Saputra', 'Baik', 'Baik', 6, 3, 'Sangat Baik'],
        ];

        foreach ($alternatif as $row) {
            [$kode, $nama] = $row;
            $a = Alternatif::updateOrCreate(['kode' => $kode], ['nama' => $nama]);
            foreach (['C1', 'C2', 'C3', 'C4', 'C5'] as $i => $kKode) {
                $k = $map[$kKode];
                $v = $row[$i + 2];
                if ($k->subKriteria->isNotEmpty()) {
                    $sub = $k->subKriteria->firstWhere('nama', $v);
                    $data = ['sub_kriteria_id' => $sub->id, 'nilai' => $sub->nilai];
                } else {
                    $data = ['sub_kriteria_id' => null, 'nilai' => $v];
                }
                Penilaian::updateOrCreate(['alternatif_id' => $a->id, 'kriteria_id' => $k->id], $data);
            }
        }
    }
}
