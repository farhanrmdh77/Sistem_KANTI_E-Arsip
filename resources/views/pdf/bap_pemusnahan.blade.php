<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pemusnahan Arsip</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 14px; line-height: 1.5; color: #000; margin: 30px; }
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3, .header h2 { margin: 0; padding: 0; }
        .content { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .ttd-box { width: 100%; margin-top: 50px; }
        .ttd-box td { border: none; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BADAN PEMERIKSA KEUANGAN REPUBLIK INDONESIA</h2>
        <h3>PERWAKILAN PROVINSI JAMBI</h3>
        <p style="margin:0; font-size: 12px;">Sistem Informasi E-Arsip Digital (KANTI)</p>
    </div>

    <div class="content">
        <h3 style="text-align: center; text-decoration: underline;">BERITA ACARA PEMUSNAHAN ARSIP</h3>
        <p style="text-align: center; margin-top: -10px;">Nomor: {{ $noBap }}</p>

        <p>Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>, bertempat di Kantor BPK Perwakilan Provinsi Jambi, telah dilaksanakan pemusnahan arsip dan penghapusan data digital (Soft Delete) dari Sistem E-Arsip. Dokumen yang dimusnahkan telah melewati masa retensi inaktif sesuai dengan Jadwal Retensi Arsip (JRA).</p>

        <p>Berikut adalah rincian dokumen kearsipan yang dimusnahkan:</p>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Kode Klasifikasi</th>
                    <th style="width: 50%;">Uraian Informasi / Nama Berkas</th>
                    <th style="width: 25%;">Tahun Berkas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($arsipTerpilih as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_arsip }}</td>
                    <td>{{ $item->nama_berkas }}</td>
                    <td style="text-align: center;">{{ $item->tahun_berkas }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 20px;">Demikian Berita Acara Pemusnahan Arsip ini dibuat dan disahkan secara digital oleh Sistem KANTI untuk dapat dipergunakan sebagaimana mestinya.</p>

        <table class="ttd-box">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    Jambi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Disahkan Oleh (Digital Audit Trail)</strong>
                    <br><br><br><br>
                    <u>{{ auth()->user()->name }}</u><br>
                    Administrator / Subbag SDM
                </td>
            </tr>
        </table>
    </div>
</body>
</html>