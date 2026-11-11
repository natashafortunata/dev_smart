<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil SPK SMART</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 32px; color: #000; }
        h2, h3 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #000; padding: 6px 8px; }
        th { background: #e5e7eb; }
        .right { text-align: right; } .center { text-align: center; }
        .ttd { margin-top: 48px; width: 260px; margin-left: auto; text-align: center; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()">Cetak</button>
    <h2>LAPORAN HASIL SISTEM PENDUKUNG KEPUTUSAN</h2>
    <h3>Metode SMART (Simple Multi Attribute Rating Technique)</h3>
    <hr>
    <p><strong>Kriteria yang digunakan:</strong></p>
    <table>
        <thead><tr><th>Kode</th><th>Nama Kriteria</th><th>Jenis</th><th>Bobot</th><th>Normalisasi</th></tr></thead>
        <tbody>
        @php $total = $kriteria->sum('bobot'); @endphp
        @foreach ($kriteria as $k)
            <tr><td class="center">{{ $k->kode }}</td><td>{{ $k->nama }}</td><td class="center">{{ ucfirst($k->jenis) }}</td><td class="right">{{ $k->bobot }}</td><td class="right">{{ $total > 0 ? number_format($k->bobot / $total, 4) : '-' }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p style="margin-top:18px"><strong>Hasil perankingan:</strong></p>
    <table>
        <thead><tr><th>Peringkat</th><th>Kode</th><th>Nama Alternatif</th><th>Nilai Akhir</th></tr></thead>
        <tbody>
        @foreach ($hasil as $h)
            <tr><td class="center">{{ $h->peringkat }}</td><td class="center">{{ $h->alternatif->kode }}</td><td>{{ $h->alternatif->nama }}</td><td class="right">{{ number_format($h->nilai_akhir, 4) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <div class="ttd">
        {{ now()->translatedFormat('d F Y') }}<br>Mengetahui,<br><br><br><br>
        <u>{{ auth()->user()->name }}</u>
    </div>
</body>
</html>
