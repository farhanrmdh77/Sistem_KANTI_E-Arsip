<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arsip;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PemusnahanController extends Controller
{
    // 1. 🌟 TAMPILKAN DAFTAR USUL MUSNAH (DENGAN FILTER & PAGINASI ANTI-LAG) 🌟
    public function index(Request $request)
    {
        // Mulai antrean query untuk arsip yang statusnya 'usul_musnah'
        $query = Arsip::where('status_pemusnahan', 'usul_musnah')->latest();

        // Cek apakah ada kata kunci pencarian dari kotak input
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_berkas', 'like', "%{$search}%")
                  ->orWhere('kode_arsip', 'like', "%{$search}%")
                  ->orWhere('tahun_berkas', 'like', "%{$search}%");
            });
        }

        // Eksekusi data dengan batas 50 dokumen per halaman agar browser tidak hang
        $arsipUsul = $query->paginate(50);

        return view('admin.usul_musnah', compact('arsipUsul'));
    }

    // 2. EKSEKUSI PEMUSNAHAN & BUAT BAP PDF
    public function prosesPemusnahan(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        $arsipTerpilih = Arsip::whereIn('id', $request->ids)->get();
        if($arsipTerpilih->isEmpty()) return back()->with('error', 'Pilih minimal 1 dokumen.');

        // Buat Nomor BAP Unik
        $noBap = 'BAP-MUSNAH/' . date('Y/m/d') . '/' . strtoupper(Str::random(5));
        
        // Cetak PDF Berita Acara
        $pdf = Pdf::loadView('pdf.bap_pemusnahan', compact('arsipTerpilih', 'noBap'))->setPaper('a4', 'potrait');
        
        // Simpan PDF ke Storage secara permanen
        $namaPdf = 'BAP_Pemusnahan_' . time() . '.pdf';
        Storage::disk('public')->put('bap/' . $namaPdf, $pdf->output());
        $pathBap = 'bap/' . $namaPdf;

        // Update Database (Soft Delete + Tautkan BAP)
        foreach($arsipTerpilih as $arsip) {
            $arsip->status_pemusnahan = 'sudah_musnah';
            $arsip->file_bap = $pathBap;
            $arsip->save();
            
            // Hapus file fisik PDF lama dari server untuk hemat penyimpanan cloud
            if ($arsip->file_dokumen && Storage::disk('public')->exists(str_replace('public/', '', $arsip->file_dokumen))) {
                Storage::disk('public')->delete(str_replace('public/', '', $arsip->file_dokumen));
                $arsip->file_dokumen = null; // Kosongkan
                $arsip->save();
            }

            // Hapus Arsip (Soft Delete: Hilang dari aplikasi, tapi ada di database)
            $arsip->delete();
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Eksekusi Pemusnahan Arsip',
            'description' => 'Mengesahkan pemusnahan ' . count($request->ids) . ' dokumen kearsipan. No BAP: ' . $noBap,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Dokumen berhasil dimusnahkan. File BAP Digital telah dibuat dan disimpan sebagai Audit Trail.');
    }

    // 3. TAMPILKAN RIWAYAT PEMUSNAHAN
    public function riwayat()
    {
        // Mengambil arsip yang statusnya sudah musnah (menggunakan withTrashed karena tadi di-softdelete)
        $riwayat = Arsip::onlyTrashed()->where('status_pemusnahan', 'sudah_musnah')->latest('deleted_at')->get();
        return view('admin.riwayat_musnah', compact('riwayat'));
    }
}