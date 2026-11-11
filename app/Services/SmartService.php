<?php

namespace App\Services;

use App\Models\Alternatif;
use App\Models\Hasil;
use App\Models\Kriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Perhitungan metode SMART (Simple Multi Attribute Rating Technique).
 *
 * Langkah:
 *  1. Normalisasi bobot      : w_j = bobot_j / Σ bobot
 *  2. Nilai utility          : benefit -> (c_i - c_min) / (c_max - c_min)
 *                              cost    -> (c_max - c_i) / (c_max - c_min)
 *                              (jika c_max = c_min, utility = 1)
 *  3. Nilai akhir            : V_i = Σ (w_j × u_ij)
 *  4. Perankingan            : urut V_i dari terbesar
 */
class SmartService
{
    /**
     * @return array{
     *   kriteria: Collection, alternatif: Collection, totalBobot: float,
     *   normalisasiBobot: array, matriks: array, minMax: array,
     *   utility: array, nilaiTerbobot: array, nilaiAkhir: array,
     *   ranking: Collection, lengkap: bool, kurang: array
     * }
     */
    public function hitung(): array
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $alternatif = Alternatif::with('penilaian')->orderBy('kode')->get();

        // 1. Normalisasi bobot
        $totalBobot = (float) $kriteria->sum('bobot');
        $normalisasiBobot = [];
        foreach ($kriteria as $k) {
            $normalisasiBobot[$k->id] = $totalBobot > 0 ? $k->bobot / $totalBobot : 0;
        }

        // Matriks keputusan
        $matriks = [];
        $kurang = [];
        foreach ($alternatif as $a) {
            $nilaiPerKriteria = $a->penilaian->keyBy('kriteria_id');
            foreach ($kriteria as $k) {
                if ($nilaiPerKriteria->has($k->id)) {
                    $matriks[$a->id][$k->id] = (float) $nilaiPerKriteria[$k->id]->nilai;
                } else {
                    $kurang[$a->id][] = $k->kode;
                }
            }
        }

        // Hanya alternatif dengan penilaian lengkap yang dihitung
        $alternatifLengkap = $alternatif->filter(fn ($a) => ! isset($kurang[$a->id]))->values();

        // 2. Nilai utility
        $minMax = [];
        foreach ($kriteria as $k) {
            $kolom = $alternatifLengkap->map(fn ($a) => $matriks[$a->id][$k->id]);
            $minMax[$k->id] = [
                'min' => $kolom->isEmpty() ? 0 : $kolom->min(),
                'max' => $kolom->isEmpty() ? 0 : $kolom->max(),
            ];
        }

        $utility = [];
        $nilaiTerbobot = [];
        $nilaiAkhir = [];
        foreach ($alternatifLengkap as $a) {
            $total = 0.0;
            foreach ($kriteria as $k) {
                $u = $this->utility(
                    $matriks[$a->id][$k->id],
                    $minMax[$k->id]['min'],
                    $minMax[$k->id]['max'],
                    $k->jenis
                );
                $utility[$a->id][$k->id] = $u;
                $nilaiTerbobot[$a->id][$k->id] = $u * $normalisasiBobot[$k->id];
                $total += $nilaiTerbobot[$a->id][$k->id];
            }
            // 3. Nilai akhir
            $nilaiAkhir[$a->id] = $total;
        }

        // 4. Perankingan (nilai sama mendapat peringkat sama)
        $ranking = $alternatifLengkap
            ->map(fn ($a) => ['alternatif' => $a, 'nilai' => round($nilaiAkhir[$a->id], 6)])
            ->sortBy([['nilai', 'desc'], [fn ($x, $y) => strcmp($x['alternatif']->kode, $y['alternatif']->kode)]])
            ->values();

        $peringkat = 0;
        $sebelumnya = null;
        $ranking = $ranking->map(function ($row, $i) use (&$peringkat, &$sebelumnya) {
            if ($sebelumnya === null || $row['nilai'] < $sebelumnya) {
                $peringkat = $i + 1;
            }
            $sebelumnya = $row['nilai'];
            $row['peringkat'] = $peringkat;

            return $row;
        });

        return [
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'totalBobot' => $totalBobot,
            'normalisasiBobot' => $normalisasiBobot,
            'matriks' => $matriks,
            'minMax' => $minMax,
            'utility' => $utility,
            'nilaiTerbobot' => $nilaiTerbobot,
            'nilaiAkhir' => $nilaiAkhir,
            'ranking' => $ranking,
            'lengkap' => empty($kurang),
            'kurang' => $kurang,
        ];
    }

    public function utility(float $nilai, float $min, float $max, string $jenis): float
    {
        if ($max == $min) {
            return 1.0;
        }

        return $jenis === 'cost'
            ? ($max - $nilai) / ($max - $min)
            : ($nilai - $min) / ($max - $min);
    }

    /**
     * Hitung lalu simpan hasil perankingan ke tabel `hasil`.
     */
    public function simpanHasil(): array
    {
        $data = $this->hitung();

        DB::transaction(function () use ($data) {
            Hasil::query()->delete();
            foreach ($data['ranking'] as $row) {
                Hasil::create([
                    'alternatif_id' => $row['alternatif']->id,
                    'nilai_akhir' => $row['nilai'],
                    'peringkat' => $row['peringkat'],
                ]);
            }
        });

        return $data;
    }
}
