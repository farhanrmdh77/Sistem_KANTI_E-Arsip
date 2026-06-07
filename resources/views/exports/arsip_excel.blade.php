<table>
    <thead>
        <tr>
            <th>kode_arsip</th>
            <th>nama_berkas</th>
            <th>tahun_berkas</th>
            <th>jumlah_berkas</th>
            <th>warna_berkas</th>
            <th>deskripsi_berkas</th>
            <th>retensi_aktif</th>
            <th>retensi_inaktif</th>
            <th>nasib_akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($arsips as $arsip)
        <tr>
            <td>{{ $arsip->kode_arsip }}</td>
            <td>{{ $arsip->nama_berkas }}</td>
            <td>
                @php
                    $rawTahun = trim($arsip->tahun_berkas);
                    if (is_numeric($rawTahun) && $rawTahun > 30000) {
                        echo (int)date('Y', (($rawTahun - 25569) * 86400));
                    } else {
                        preg_match('/\d{4}/', $rawTahun, $matches);
                        echo !empty($matches) ? $matches[0] : $rawTahun;
                    }
                @endphp
            </td>
            <td>{{ $arsip->jumlah_berkas ?? 1 }}</td>
            <td>{{ $arsip->warna_berkas ?? '-' }}</td>
            <td>{{ $arsip->deskripsi_berkas }}</td>
            <td>{{ $arsip->retensi_aktif ?? 0 }}</td>
            <td>{{ $arsip->retensi_inaktif ?? 0 }}</td>
            <td>{{ $arsip->nasib_akhir ?? 'Musnah' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>