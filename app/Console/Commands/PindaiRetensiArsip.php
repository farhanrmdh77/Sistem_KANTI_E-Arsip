<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Arsip;

class PindaiRetensiArsip extends Command
{
    protected $signature = 'arsip:pindai-retensi';
    protected $description = 'Mendeteksi arsip yang telah habis masa retensinya untuk diusulkan musnah.';

    public function handle()
    {
        $tahunSekarang = (int) date('Y');
        // Ambil semua arsip yang belum musnah dan nasib akhirnya BUKAN permanen
        $arsips = Arsip::where('status_pemusnahan', 'aktif')
                       ->whereRaw('LOWER(nasib_akhir) != ?', ['permanen'])
                       ->get();

        $count = 0;
        foreach ($arsips as $arsip) {
            // Ambil 4 digit angka tahun dari string (misal: "Tahun 2018" jadi 2018)
            preg_match('/\d{4}/', $arsip->tahun_berkas, $matches);
            $tahunBerkas = !empty($matches) ? (int)$matches[0] : 0;

            $retensiAktif = (int) $arsip->retensi_aktif;
            $retensiInaktif = (int) $arsip->retensi_inaktif;

            $tahunKedaluwarsa = $tahunBerkas + $retensiAktif + $retensiInaktif;

            // Jika tahun saat ini sudah MELEWATI tahun kedaluwarsa
            if ($tahunBerkas > 0 && $tahunSekarang > $tahunKedaluwarsa) {
                $arsip->status_pemusnahan = 'usul_musnah';
                $arsip->save();
                $count++;
            }
        }

        $this->info("Berhasil! Ditemukan {$count} dokumen baru yang diusulkan untuk dimusnahkan.");
    }
}