<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Hasil;
use App\Models\Kriteria;
use App\Models\User;
use App\Services\SmartService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_perhitungan_smart_sesuai_hitung_manual(): void
    {
        $data = app(SmartService::class)->hitung();

        $hasil = $data['ranking']->mapWithKeys(fn ($r) => [$r['alternatif']->kode => $r['nilai']]);

        $this->assertEqualsWithDelta(0.675, $hasil['A2'], 1e-6);
        $this->assertEqualsWithDelta(0.650, $hasil['A1'], 1e-6);
        $this->assertEqualsWithDelta(0.525, $hasil['A5'], 1e-6);
        $this->assertEqualsWithDelta(0.500, $hasil['A4'], 1e-6);
        $this->assertEqualsWithDelta(0.325, $hasil['A3'], 1e-6);
        $this->assertSame(['A2', 'A1', 'A5', 'A4', 'A3'], $data['ranking']->pluck('alternatif.kode')->all());
        $this->assertEqualsWithDelta(1.0, array_sum($data['normalisasiBobot']), 1e-9);
    }

    public function test_utility_benefit_cost_dan_nilai_sama(): void
    {
        $s = new SmartService;
        $this->assertSame(1.0, $s->utility(10, 0, 10, 'benefit'));
        $this->assertSame(0.0, $s->utility(10, 0, 10, 'cost'));
        $this->assertSame(1.0, $s->utility(5, 5, 5, 'cost'));
    }

    public function test_alternatif_belum_lengkap_tidak_dihitung(): void
    {
        Alternatif::create(['kode' => 'A6', 'nama' => 'Baru']);
        $data = app(SmartService::class)->hitung();

        $this->assertFalse($data['lengkap']);
        $this->assertCount(5, $data['ranking']);
    }

    public function test_admin_dapat_menyimpan_hasil(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->post(route('perhitungan.simpan'))->assertRedirect(route('hasil.index'));
        $this->assertSame('A2', Hasil::where('peringkat', 1)->first()->alternatif->kode);
    }

    public function test_semua_halaman_admin_dapat_dibuka(): void
    {
        $admin = User::where('role', 'admin')->first();
        $k = Kriteria::first();
        $a = Alternatif::first();
        app(SmartService::class)->simpanHasil();

        foreach ([
            route('dashboard'), route('kriteria.index'), route('kriteria.create'), route('kriteria.show', $k),
            route('kriteria.edit', $k), route('alternatif.index'), route('alternatif.create'),
            route('alternatif.edit', $a), route('penilaian.index'), route('penilaian.edit', $a),
            route('perhitungan.index'), route('hasil.index'), route('hasil.cetak'), route('users.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_pimpinan_tidak_bisa_mengubah_data_master(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        $this->actingAs($pimpinan)->get(route('hasil.index'))->assertOk();
        $this->actingAs($pimpinan)->get(route('kriteria.index'))->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_simpan_penilaian(): void
    {
        $admin = User::where('role', 'admin')->first();
        $a = Alternatif::create(['kode' => 'A9', 'nama' => 'Uji']);
        $nilai = [];
        foreach (Kriteria::with('subKriteria')->get() as $k) {
            $nilai[$k->id] = $k->subKriteria->isNotEmpty() ? $k->subKriteria->first()->id : 4;
        }

        $this->actingAs($admin)->put(route('penilaian.update', $a), ['nilai' => $nilai])
            ->assertRedirect(route('penilaian.index'));
        $this->assertSame(Kriteria::count(), $a->penilaian()->count());
    }
}
