@extends('layouts.app')

@section('title', 'Verifikasi Akses - ' . \App\Models\Setting::getAppName())

@push('styles')
<style>
    .qr-card { max-width: 500px; border-radius: 20px; border: 1px solid #e2e8f0; background: #ffffff; }
    .doc-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
    .input-tamu { border-radius: 12px; padding: 14px 15px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 14px; }
    .input-tamu:focus { border-color: #C8A35A; box-shadow: 0 0 0 3px rgba(200, 163, 90, 0.15); outline: none; background: #ffffff; }

    /* DARK MODE */
    body.dark-mode .qr-card { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .doc-box { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .alert-info { background-color: rgba(59, 130, 246, 0.1) !important; border-color: rgba(59, 130, 246, 0.2) !important; color: #38bdf8 !important; }
    body.dark-mode .input-tamu { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    body.dark-mode .input-tamu:focus { background: #0b1120 !important; border-color: #C8A35A !important; }
</style>
@endpush

@section('content')
<div class="container mt-5 px-3 text-center">
    <div class="card qr-card shadow-lg mx-auto p-2 p-md-4">
        <div class="card-body p-4 p-md-5">
            <i class="fa-solid fa-lock text-warning mb-3" style="font-size: 55px;"></i>
            <h4 class="fw-bold text-dark">Dokumen Terkunci</h4>
            <p class="text-muted small">Anda memindai QR Code untuk dokumen berikut:</p>
            
            <div class="p-3 doc-box mb-4 text-start text-md-center">
                <span class="badge bg-secondary mb-2 px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-tag"></i> {{ $arsip->kode_arsip }}</span>
                <h6 class="fw-bold mb-0 text-dark" style="line-height: 1.5; font-size: 15px;">{{ $arsip->nama_berkas }}</h6>
            </div>

            {{-- OPSI 1: MASIH MENUNGGU (UI BACKUP JIKA POP-UP TERTUTUP) --}}
            @if($cekIzin && $cekIzin->status == 'menunggu')
                <div class="alert alert-info py-4 shadow-sm border-0 rounded-3">
                    <div class="spinner-border spinner-border-sm text-info mb-2" role="status" style="width: 25px; height: 25px;"></div>
                    <h6 class="fw-bold mb-1 mt-2">Menunggu Verifikasi...</h6>
                    <p class="small mb-0 opacity-75">Tunggu di halaman ini. Dokumen akan terbuka otomatis setelah Admin menyetujui.</p>
                </div>

            {{-- OPSI 2: SUDAH DISETUJUI --}}
            @elseif($cekIzin && $cekIzin->status == 'disetujui')
                <div class="alert alert-success py-3 shadow-sm border-0 rounded-3">
                    <div class="fw-bold mb-2"><i class="fa-solid fa-circle-check"></i> Akses Telah Disetujui!</div>
                    @if($arsip->file_dokumen)
                        {{-- 🌟 PERBAIKAN: Tombol manual ini sekarang diarahkan ke Halaman Preview 🌟 --}}
                        <a href="{{ route('qr.preview_file', $arsip->id) }}" class="btn btn-success mt-1 px-4 py-2 fw-bold rounded-pill shadow-sm w-100">
                            <i class="fa-solid fa-eye me-1"></i> Buka & Tinjau Dokumen
                        </a>
                    @endif
                </div>
            
            {{-- OPSI 3: DITOLAK --}}
            @elseif($cekIzin && $cekIzin->status == 'ditolak')
                <div class="alert alert-danger py-3 shadow-sm border-0 rounded-3">
                    <div class="fw-bold"><i class="fa-solid fa-circle-xmark"></i> Permintaan Ditolak</div>
                    <p class="small mb-0 mt-1">Admin tidak memberikan izin akses untuk dokumen ini.</p>
                </div>

            {{-- OPSI 4: BELUM MINTA IZIN --}}
            @else
                <form action="{{ route('qr.minta_izin', $arsip->id) }}" method="POST" class="m-0 text-start" onsubmit="tampilkanLoadingAwal()">
                    @csrf
                    <input type="hidden" name="guest_id" value="{{ $guestId }}">

                    @if(!Auth::check())
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold" style="font-size: 13px;">Identitas Pemohon (Wajib):</label>
                        <input type="text" name="nama_tamu" class="form-control input-tamu" placeholder="Contoh: Fikri (Magang)" required>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" style="font-size: 15px;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Minta Akses Sekarang
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Asumsi kamu menjalankan ini saat user klik "Minta Izin" atau saat halaman loading dimuat
        // Ini adalah Radar yang mengecek ke server setiap 3 detik (3000 ms)
        let checkInterval = setInterval(function() {
            
            fetch("{{ route('qr.check_status', $arsip->id) }}")
                .then(response => response.json())
                .then(data => {
                    
                    // JIKA ADMIN MENEKAN "SETUJUI"
                    if(data.status === 'disetujui') {
                        clearInterval(checkInterval); // Matikan radar agar tidak nge-loop
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Akses Disetujui!',
                            text: 'Admin telah memberikan izin. Mengalihkan ke dokumen...',
                            showConfirmButton: false,
                            timer: 2000 // Tampil selama 2 detik
                        }).then(() => {
                            // 🌟 ALIHKAN BROWSER HP PEGAWAI SECARA OTOMATIS KE FILE PDF 🌟
                            window.location.href = data.file_url; 
                        });
                    } 
                    // JIKA ADMIN MENEKAN "TOLAK"
                    else if(data.status === 'ditolak') {
                        clearInterval(checkInterval);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Ditolak',
                            text: 'Pimpinan/Admin tidak memberikan izin untuk dokumen ini.',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#ef4444'
                        }).then(() => {
                            window.location.reload(); // Refresh halaman
                        });
                    }
                })
                .catch(error => console.error('Error mengecek status:', error));
                
        }, 3000); 
    });

    // 🌟 SCRIPT RADAR POP-UP TAHAN BANTING JIKA STATUS MENUNGGU 🌟
    @if($cekIzin && $cekIzin->status == 'menunggu')
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: 'Menunggu Admin...',
                html: '<span style="font-size:14px; color:#64748b;">Jangan tutup halaman ini.<br>Sistem sedang mengecek persetujuan Admin BPK...</span>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Lakukan polling ngecek ke server setiap 2.5 detik
                    let radarInterval = setInterval(function() {
                        fetch("{{ route('qr.check_status', $arsip->id) }}?guest_id={{ $guestId }}")
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'disetujui' && data.file_url) {
                                clearInterval(radarInterval); // Matikan radar
                                
                                // Ganti animasi menjadi Sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Akses Disetujui!',
                                    text: 'Membuka dokumen secara otomatis...',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    // 🌟 TERLEMPAR KE HALAMAN PREVIEW 🌟
                                    window.location.replace(data.file_url);
                                });
                            } else if (data.status === 'ditolak') {
                                clearInterval(radarInterval);
                                window.location.reload(); // Refresh untuk tampilkan UI ditolak
                            }
                        })
                        .catch(error => console.log('Radar berjalan...'));
                    }, 2500);
                }
            });
        });
    @endif
</script>
@endpush