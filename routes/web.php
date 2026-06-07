<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrashController; 
use App\Http\Controllers\IzinAksesController;
use App\Http\Controllers\PemusnahanController;
use Illuminate\Support\Facades\Route;

// ====================================================================
// 🟢 RUTE TERBUKA (BISA DIAKSES SIAPA SAJA, TANPA LOGIN)
// ====================================================================
Route::get('/qr/scan/{id}', [IzinAksesController::class, 'scanQR'])->name('qr.scan');
Route::get('/folder/{kode}', [ArsipController::class, 'folderIsi'])->name('arsip.folder.isi');

// Pendaratan Cerdas dari Notifikasi Menuju Halaman Paginasi
// 🌟 Pendaratan Cerdas dari Notifikasi & Pencarian Menuju Halaman Paginasi 🌟
// 🌟 Pendaratan Cerdas dari Notifikasi & Pencarian Menuju Halaman Paginasi 🌟
Route::get('/arsip/pendaratan/{id}', function($id) {
    // 1. Cari arsip (Termasuk yang di tong sampah untuk pengecekan awal)
    $arsip = \App\Models\Arsip::withTrashed()->find($id);
    
    if (!$arsip) {
        return redirect()->route('arsip.dashboard')->with('error', 'Dokumen tersebut sudah dihapus permanen dari sistem.');
    }

    // 2. Jika dokumen ternyata ada di Tong Sampah, arahkan admin ke Kelola Sampah
    if ($arsip->trashed()) {
        return redirect()->route('arsip.trash')->with('info', 'Dokumen yang Anda tuju sedang berada di dalam Tong Sampah.');
    }
    
    // 3. Deteksi kode induk cerdas (Mengatasi error LAINNYA.00)
    $kodeLengkap = $arsip->kode_arsip;
    if (strpos($kodeLengkap, '.') !== false) {
        $docInduk = explode('.', $kodeLengkap);
        $linkInduk = $docInduk[0] . '.' . ($docInduk[1] ?? '00');
    } else {
        $linkInduk = $kodeLengkap;
    }
    
    // 4. MENGHITUNG POSISI HALAMAN SECARA AKURAT
    // Menghapus withTrashed() agar tabel yang dihitung HANYA data aktif.
    // Menambahkan orderBy('id', 'desc') sebagai pengunci absolut jika jam uploadnya sama.
    $allArsip = \App\Models\Arsip::where('kode_arsip', 'like', $linkInduk . '%')
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->pluck('id')
        ->toArray();
        
    $posisi = array_search($arsip->id, $allArsip);
    
    // Asumsi tabel menampilkan 10 baris per halaman
    $page = ($posisi !== false) ? floor($posisi / 10) + 1 : 1;
    
    return redirect()->to(route('arsip.folder.isi', ['kode' => $linkInduk, 'page' => $page]) . '#arsip-' . $arsip->id);
})->name('arsip.pendaratan');

Route::post('/qr/request/{arsip_id}', [IzinAksesController::class, 'mintaIzin'])->name('qr.minta_izin');
Route::get('/qr/check-status/{arsip_id}', [IzinAksesController::class, 'checkStatus'])->name('qr.check_status');
Route::get('/qr/preview/{id}', [IzinAksesController::class, 'previewFile'])->name('qr.preview_file');
Route::get('/qr/buka-file/{id}', [IzinAksesController::class, 'bukaFile'])->name('qr.buka_file');

// --- RUTE LOGIN & LOGOUT ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ====================================================================
// 🔴 RUTE APLIKASI UTAMA (WAJIB LOGIN)
// ====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/', [ArsipController::class, 'dashboard'])->name('arsip.dashboard');
    Route::get('/folders', [ArsipController::class, 'folders'])->name('arsip.folders');
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::get('/pencarian-global', [ArsipController::class, 'globalSearch'])->name('arsip.global_search');
    Route::get('/folder/{kode}/tambah', [ArsipController::class, 'create'])->name('arsip.create');
    Route::post('/arsip/store', [ArsipController::class, 'store'])->name('arsip.store');
    Route::get('/folder/{kode}/cetak-pdf', [ArsipController::class, 'cetakPDF'])->name('arsip.cetak_pdf');
    Route::get('/export-arsip', [ArsipController::class, 'export'])->name('arsip.export');
    Route::put('/profile/update-avatar', [UserController::class, 'updateAvatar'])->name('profile.update_avatar');

    // 🛡️ HANYA ADMIN 🛡️
    Route::middleware('can:admin')->group(function () {
        
        // --- MANAJEMEN ARSIP ---
        Route::get('/arsip/download-template', [ArsipController::class, 'downloadTemplate'])->name('arsip.download_template');
        Route::get('/import-arsip', function () { return view('import'); })->name('arsip.import.form');
        Route::post('/import-arsip', [ArsipController::class, 'import'])->name('arsip.import');
        Route::delete('/arsip/bulk-delete', [ArsipController::class, 'bulkDelete'])->name('arsip.bulk_delete');
        Route::get('/arsip/{id}/edit', [ArsipController::class, 'edit'])->name('arsip.edit');
        Route::put('/arsip/{id}', [ArsipController::class, 'update'])->name('arsip.update');
        Route::delete('/arsip/{id}', [ArsipController::class, 'destroy'])->name('arsip.destroy');
        Route::delete('/arsip/{id}/hapus-file', [ArsipController::class, 'hapusFile'])->name('arsip.hapus_file');
        Route::put('/arsip/{id}/update-kp', [ArsipController::class, 'updateKP'])->name('arsip.update_kp');
        
        // --- MANAJEMEN PENGGUNA ---
        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/pengguna/{id}', [UserController::class, 'update'])->name('users.update'); 
        Route::delete('/pengguna/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/pengguna/{id}/reset', [UserController::class, 'resetPassword'])->name('users.reset');
        
        // --- ACTIVITY LOGS ---
        Route::get('/logs', function(\Illuminate\Http\Request $request) { 
            $query = \App\Models\ActivityLog::with('user')->latest(); 
            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('created_at', $request->bulan)->whereYear('created_at', $request->tahun);
            }
            $logs = $query->paginate(50)->withQueryString(); 
            return view('logs.index', compact('logs')); 
        })->name('logs.index');
        
        // --- TONG SAMPAH ---
        Route::get('/tong-sampah', [TrashController::class, 'index'])->name('arsip.trash');
        Route::post('/tong-sampah/arsip/{id}/restore', [TrashController::class, 'restoreArsip'])->name('arsip.restore');
        Route::delete('/tong-sampah/arsip/{id}/force-delete', [TrashController::class, 'forceDeleteArsip'])->name('arsip.force_delete');
        Route::post('/tong-sampah/folder/{id}/restore', [TrashController::class, 'restoreFolder'])->name('folders.restore');
        Route::delete('/tong-sampah/folder/{id}/force-delete', [TrashController::class, 'forceDeleteFolder'])->name('folders.force_delete');
        
        // --- PENGATURAN ---
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/pengaturan/backup', [SettingController::class, 'backupDatabase'])->name('settings.backup');
        
        // --- MANAJEMEN FOLDER ---
        Route::post('/folders/store', [ArsipController::class, 'storeFolder'])->name('folders.store');
        Route::put('/folders/{id}', [ArsipController::class, 'updateFolder'])->name('folders.update');
        Route::delete('/folders/{id}', [ArsipController::class, 'destroyFolder'])->name('folders.destroy');
        
        // --- PUSAT VERIFIKASI AKSES (QR CODE) ---
        Route::get('/admin/permintaan-akses', [IzinAksesController::class, 'indexAdmin'])->name('admin.permintaan_akses');
        Route::put('/admin/permintaan-akses/{id}', [IzinAksesController::class, 'verifikasi'])->name('admin.verifikasi_izin');
        Route::put('/admin/permintaan-akses/{id}/update', [IzinAksesController::class, 'updateIzin'])->name('admin.izin.update');
        Route::delete('/admin/permintaan-akses/{id}', [IzinAksesController::class, 'destroy'])->name('admin.izin.destroy');
        // Rute untuk Radar AJAX Polling
        Route::get('/admin/cek-notif-akses', [IzinAksesController::class, 'cekNotifBaru'])->name('admin.cek_notif');

        // --- DAFTAR USUL MUSNAH ---
        Route::get('/admin/usul-musnah', [PemusnahanController::class, 'index'])->name('admin.usul_musnah');
        Route::post('/admin/usul-musnah/proses', [PemusnahanController::class, 'prosesPemusnahan'])->name('admin.proses_musnah');
        Route::get('/admin/riwayat-pemusnahan', [PemusnahanController::class, 'riwayat'])->name('admin.riwayat_pemusnahan');
    });
});