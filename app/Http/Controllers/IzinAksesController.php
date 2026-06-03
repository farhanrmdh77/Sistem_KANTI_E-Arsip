<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IzinAkses;
use App\Models\Arsip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class IzinAksesController extends Controller
{
    // 1. HALAMAN SCAN QR DOKUMEN
    public function scanQR(Request $request, $id)
    {
        $arsip = Arsip::find($id);

        if (!$arsip) { return abort(404); }
        
        if (empty($arsip->file_dokumen)) {
            return response('
                <html lang="id">
                <head><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dokumen Belum Tersedia</title><style>body { font-family: "Segoe UI", sans-serif; background: #0b1120; color: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; text-align: center; } .card { background: #1e293b; padding: 40px; border-radius: 20px; border: 1px solid #334155; } h2 { color: #C8A35A; }</style></head>
                <body><div class="card"><h2 style="font-size: 50px; margin:0;">🗂️</h2><h2>File Belum Diunggah</h2><p style="color:#94a3b8;">Admin belum mengunggah salinan digital (PDF) untuk dokumen ini.</p></div></body>
                </html>
            ', 200);
        }

        // Trik Cookie & ID di URL agar HP Tamu tidak amnesia saat direfresh
        $guestId = $request->query('guest_id');
        if (!$guestId) { $guestId = $request->cookie('kanti_guest_id'); }
        if (!$guestId) { $guestId = (string) Str::uuid(); }
        Cookie::queue('kanti_guest_id', $guestId, 60 * 24 * 7);

        // Cek Riwayat Izin
        $cekIzin = IzinAkses::where('arsip_id', $id)
            ->where(function($query) use ($guestId) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', $guestId); 
                }
            })
            ->orderBy('created_at', 'desc')
            ->first();

        // 🌟 Jika Disetujui, Langsung Arahkan ke Halaman Preview PDF
        if ($cekIzin && $cekIzin->status == 'disetujui' && $arsip->file_dokumen) {
            return redirect()->route('qr.preview_file', $id);
        }

        return view('qr.scan', compact('arsip', 'cekIzin', 'guestId'));
    }

    // 2. MEMPROSES KLIK TOMBOL "MINTA IZIN" (Menangkap Nama & HP Pegawai)
    public function mintaIzin(Request $request, $arsip_id)
    {
        $agent = $request->header('User-Agent');
        $os = preg_match('/android/i', $agent) ? 'HP Android' : (preg_match('/iphone|ipad/i', $agent) ? 'Apple iOS' : 'PC/Laptop');
        $browser = preg_match('/chrome|crios/i', $agent) ? 'Chrome' : (preg_match('/safari/i', $agent) ? 'Safari' : 'Browser');
        
        $deviceInfo = $os . ' (' . $browser . ')';

        if (!Auth::check() && $request->has('nama_tamu')) {
            $deviceInfo = strip_tags($request->nama_tamu) . ' | ' . $deviceInfo; 
        }

        $guestId = $request->guest_id;
        Cookie::queue('kanti_guest_id', $guestId, 60 * 24 * 7); 

        IzinAkses::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'session_id' => $guestId, 
            'device_info' => $deviceInfo,
            'arsip_id' => $arsip_id,
            'status' => 'menunggu',
            'hak_unduh' => false 
        ]);

        return redirect()->route('qr.scan', ['id' => $arsip_id, 'guest_id' => $guestId]);
    }

    // 3. RADAR PENGECEK STATUS OTOMATIS (AJAX)
    // FUNGSI RADAR UNTUK PEGAWAI (CEK STATUS IZIN)
    public function checkStatus(Request $request, $arsip_id)
    {
        $guestId = $request->query('guest_id') ?? $request->cookie('kanti_guest_id'); 

        $cekIzin = \App\Models\IzinAkses::where('arsip_id', $arsip_id)
            ->where(function($query) use ($guestId) {
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                } else {
                    $query->where('session_id', $guestId);
                }
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($cekIzin) {
            $arsip = \App\Models\Arsip::find($arsip_id);
            return response()->json([
                'status' => $cekIzin->status,
                'hak_unduh' => (bool) $cekIzin->hak_unduh, // 🌟 INI TAMBAHANNYA: Kirim status hak unduh (true/false)
                'file_url' => ($cekIzin->status == 'disetujui' && $arsip->file_dokumen) ? route('qr.preview_file', $arsip_id) : null
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }
    
    // 4. HALAMAN PREVIEW PDF & DETAIL (Hanya yang Disetujui)
    public function previewFile(Request $request, $id)
    {
        $arsip = Arsip::findOrFail($id);
        if (!$arsip->file_dokumen) { return abort(404, 'File digital belum tersedia.'); }

        // Keamanan: Cek apakah HP ini benar-benar punya hak (Sudah disetujui / Admin)
        $guestId = $request->cookie('kanti_guest_id');
        $cekIzin = IzinAkses::where('arsip_id', $id)
            ->where(function($query) use ($guestId) {
                if (Auth::check()) { $query->where('user_id', Auth::id()); } 
                else { $query->where('session_id', $guestId); }
            })->where('status', 'disetujui')->first();

        if (!Auth::check() || (Auth::check() && Auth::user()->role != 'admin')) {
            if (!$cekIzin) { return redirect()->route('qr.scan', $id); }
        }

        return view('qr.preview', compact('arsip', 'cekIzin'));
    }

    // 5. JALUR VVIP: BUKA RAW FILE (Dipakai di dalam iframe Preview / Tombol Unduh)
    public function bukaFile(\Illuminate\Http\Request $request, $id)
    {
        $arsip = Arsip::findOrFail($id);
        if (!$arsip->file_dokumen) { return abort(404, 'File digital belum tersedia.'); }

        $namaFile = str_replace('public/', '', $arsip->file_dokumen);
        $path = storage_path('app/public/' . $namaFile);

        if (file_exists($path)) { 
            // Deteksi jenis file (PDF atau Gambar)
            $mimeType = mime_content_type($path);
            
            // 🌟 JURUS JITU: Jika link ada ?download=1, paksa Unduh. Jika tidak, tampilkan (inline) 🌟
            $disposition = $request->has('download') ? 'attachment' : 'inline';
            
            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => $disposition . '; filename="Dokumen_Arsip_' . $arsip->kode_arsip . '.' . pathinfo($path, PATHINFO_EXTENSION) . '"'
            ]);
        }
        
        return abort(404, 'File fisik tidak ditemukan.');
    }

    // 6. TAMPILAN DASHBOARD ADMIN
    public function indexAdmin()
    {
        $permintaan = IzinAkses::with(['user', 'arsip'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.permintaan_akses', compact('permintaan'));
    }

    // 7. PROSES ADMIN MENEKAN TOMBOL SETUJUI/TOLAK
    public function verifikasi(Request $request, $id)
    {
        $izin = IzinAkses::findOrFail($id);
        
        $izin->update([
            'status' => $request->status, 
            'hak_unduh' => $request->has('hak_unduh') ? true : false
        ]);
        
        return back()->with('success', 'Status izin diperbarui!');
    }

    public function updateIzin(Request $request, $id)
    {
        $izin = \App\Models\IzinAkses::findOrFail($id);
        
        // Update status dan hak unduh sesuai permintaan admin
        $izin->status = $request->status; // 'disetujui' atau 'ditolak'
        $izin->hak_unduh = $request->has('hak_unduh') ? true : false;
        $izin->save();

        return redirect()->back()->with('success', 'Status akses berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $izin = \App\Models\IzinAkses::findOrFail($id);
        
        // Ambil nama untuk dicatat di log sebelum dihapus
        $namaBerkas = $izin->arsip ? $izin->arsip->nama_berkas : 'Dokumen Terhapus';
        $pemohon = $izin->user ? $izin->user->name : ($izin->device_info ?? 'Tamu');

        $izin->delete();

        // Catat aktivitas penghapusan ini ke log sistem
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Hapus Riwayat Izin',
            'description' => 'Menghapus riwayat permintaan akses dari ' . $pemohon . ' untuk arsip: ' . $namaBerkas,
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Riwayat permohonan akses berhasil dihapus secara permanen!');
    }

    public function checkPermintaanBaru()
    {
        // Cek apakah ada status 'menunggu' di tabel
        $jumlah = \App\Models\IzinAkses::where('status', 'menunggu')->count();
        return response()->json(['jumlah' => $jumlah]);
    }

    // FUNGSI RADAR NOTIFIKASI REAL-TIME
    public function cekNotifBaru()
    {
        $jumlah = \App\Models\IzinAkses::where('status', 'menunggu')->count();
        return response()->json(['jumlah' => $jumlah]);
    }
}