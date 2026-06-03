@extends('layouts.app')

@section('title', 'Tinjau Dokumen - ' . $arsip->kode_arsip)
@section('header_title', 'Tinjau Dokumen')

@push('styles')
<style>
    .pdf-container {
        width: 100%;
        height: 75vh;
        border-radius: 12px;
        overflow-y: auto; 
        border: 1px solid #e2e8f0;
        background-color: #334155;
        padding: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .pdf-container canvas {
        max-width: 100%;
        margin-bottom: 20px; 
        border-radius: 4px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }

    /* Efek untuk Gambar Foto */
    .img-preview {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 8px;
        pointer-events: none; /* Mencegah drag/save image langsung di HP */
    }

    .detail-card { border-radius: 16px; border: 1px solid #e2e8f0; }
    
    body.dark-mode .pdf-container { border-color: #1e293b; background-color: #0b1120; }
    body.dark-mode .detail-card { background-color: #0f172a; border-color: #1e293b; }
    body.dark-mode .text-dark { color: #f8fafc !important; }
</style>
@endpush

@section('content')

{{-- 🌟 DETEKSI APAKAH FILE INI GAMBAR ATAU PDF 🌟 --}}
@php
    $ext = strtolower(pathinfo($arsip->file_dokumen, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['png', 'jpg', 'jpeg']);
@endphp

<div class="container-fluid py-2">
    
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-light border shadow-sm fw-bold rounded-pill px-3" style="font-size: 13px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- Sisi Kiri: Pembaca File --}}
        <div class="col-lg-8">
            <div id="pdf-viewer" class="shadow-sm pdf-container {{ $isImage ? 'justify-content-center' : '' }}">
                
                @if($isImage)
                    {{-- TAMPILAN JIKA BERUPA FOTO/GAMBAR --}}
                    <img src="{{ route('qr.buka_file', $arsip->id) }}" alt="Preview Dokumen" class="img-preview shadow" oncontextmenu="return false;">
                @else
                    {{-- TAMPILAN JIKA BERUPA PDF --}}
                    <div id="loading-msg" class="text-white mt-5">
                        <i class="fa-solid fa-spinner fa-spin mb-2" style="font-size: 24px; color: #C8A35A;"></i><br>
                        <span class="fw-bold">Mempersiapkan Dokumen PDF...</span>
                    </div>
                @endif

            </div>
            <small class="text-muted mt-2 d-block text-center">* Fitur unduh dan cetak telah dinonaktifkan untuk menjaga keamanan dokumen.</small>
        </div>

        {{-- Sisi Kanan: Detail Arsip --}}
        <div class="col-lg-4">
            <div class="card detail-card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" style="color: #C8A35A;">
                        <i class="fa-solid fa-circle-info me-1"></i> Rincian Dokumen
                    </h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase">Nama Berkas</label>
                        <div class="fw-bold text-dark" style="font-size: 16px;">{{ $arsip->nama_berkas }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase">Kode KP</label>
                        <div><span class="badge bg-secondary p-2 px-3 rounded-pill">{{ $arsip->kode_arsip }}</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase">Tahun Terbit</label>
                        <div class="text-dark fw-medium">{{ $arsip->tahun_berkas }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase">Lokasi Fisik Asli</label>
                        <div class="text-dark fw-medium">
                            <i class="fa-solid fa-box-archive text-warning me-1"></i> {{ $arsip->status_lokasi ?? 'Penyimpanan Internal' }}
                        </div>
                    </div>

                    @if($arsip->deskripsi_berkas)
                    <div class="mb-4">
                        <label class="text-muted small fw-bold text-uppercase">Catatan / Deskripsi</label>
                        <div class="text-dark small mt-1" style="line-height: 1.5;">{{ $arsip->deskripsi_berkas }}</div>
                    </div>
                    @endif

                    <hr class="my-4" style="opacity: 0.1;">

                    {{-- 🌟 LOGIKA PENGUNCIAN & RADAR TOMBOL UNDUH 🌟 --}}
                    @if(Auth::check() && Auth::user()->role == 'admin')
                        <a href="{{ route('qr.buka_file', $arsip->id) }}?download=1" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-download me-1"></i> Unduh File Asli (Admin)
                        </a>
                    @else
                        {{-- Container Khusus Pegawai agar bisa dikendalikan via AJAX --}}
                        <div id="downloadAreaPegawai">
                            
                            {{-- Tombol Boleh Unduh (Disembunyikan jika hak_unduh false) --}}
                            <a href="{{ route('qr.buka_file', $arsip->id) }}?download=1" id="btnDownloadFile" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="display: {{ (isset($cekIzin) && $cekIzin->hak_unduh) ? 'inline-block' : 'none' }};">
                                <i class="fa-solid fa-download me-1"></i> Unduh File Asli
                            </a>

                            {{-- Tombol Akses Terkunci (Disembunyikan jika hak_unduh true) --}}
                            <div id="btnLockedDownload" style="display: {{ (!isset($cekIzin) || !$cekIzin->hak_unduh) ? 'block' : 'none' }};">
                                <button class="btn btn-secondary w-100 rounded-pill py-2 fw-bold shadow-sm" disabled style="opacity: 0.7;">
                                    <i class="fa-solid fa-ban me-1"></i> Akses Unduh Terkunci
                                </button>
                                <div class="text-center mt-2 small text-danger fw-bold">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Anda hanya diizinkan membaca dokumen secara digital.
                                </div>
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- 🌟 JALANKAN PDF.JS HANYA JIKA FILE BUKAN GAMBAR 🌟 --}}
@if(!$isImage)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const url = "{{ route('qr.buka_file', $arsip->id) }}";
        const viewer = document.getElementById('pdf-viewer');
        const loadingMsg = document.getElementById('loading-msg');

        pdfjsLib.getDocument(url).promise.then(pdf => {
            loadingMsg.style.display = 'none'; 
            
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(page => {
                    const canvas = document.createElement('canvas');
                    viewer.appendChild(canvas);
                    const ctx = canvas.getContext('2d');
                    
                    const outputScale = window.devicePixelRatio || 1;
                    const viewport = page.getViewport({ scale: 2.0 });

                    canvas.width = Math.floor(viewport.width * outputScale);
                    canvas.height = Math.floor(viewport.height * outputScale);
                    canvas.style.width = "100%";  
                    canvas.style.height = "auto"; 

                    const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

                    const renderContext = { canvasContext: ctx, transform: transform, viewport: viewport };
                    page.render(renderContext);
                });
            }
        }).catch(error => {
            loadingMsg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-danger mb-2" style="font-size: 30px;"></i><br><span class="text-white">Gagal memuat dokumen.</span>';
        });
    });
</script>
@endif

{{--  RADAR REAL-TIME UNTUK PEGAWAI  --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const arsipId = "{{ $arsip->id ?? '' }}"; 
        const isAdmin = {{ (Auth::check() && Auth::user()->role == 'admin') ? 'true' : 'false' }};

        // Radar hanya dijalankan jika yang membuka BUKAN admin (karena admin hak aksesnya tidak dicabut)
        if (arsipId && !isAdmin) {
            
            let checkInterval = setInterval(function() {
                fetch(`/qr/check-status/${arsipId}`)
                .then(response => response.json())
                .then(data => {
                    
                    // 1. JIKA ADMIN MENEKAN "CABUT" ATAU MENGHAPUS IZIN (TONG SAMPAH)
                    if (data.status === 'ditolak' || data.status === 'not_found') {
                        clearInterval(checkInterval); // Matikan radar agar tidak loop
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Dicabut!',
                            text: 'Admin telah mencabut atau menghapus izin akses Anda terhadap dokumen ini.',
                            allowOutsideClick: false,
                            confirmButtonText: 'Tutup Dokumen',
                            confirmButtonColor: '#ef4444',
                            backdrop: `rgba(15, 23, 42, 0.95)` // Latar gelap pekat agar PDF dibelakang tidak bisa dibaca
                        }).then(() => {
                            // 🌟 PERBAIKAN: Arahkan ke rute publik (qr.scan) agar muncul pemberitahuan Ditolak (bukan ke Login) 🌟
                            window.location.href = "{{ route('qr.scan', $arsip->id) }}"; 
                        });
                    } 
                    
                    // 2. JIKA STATUS MASIH "DISETUJUI", TAPI SAKELAR UNDUH DIUBAH
                    else if (data.status === 'disetujui') {
                        const btnDownload = document.getElementById('btnDownloadFile');
                        const btnLocked = document.getElementById('btnLockedDownload');
                        
                        if (btnDownload && btnLocked) {
                            if (data.hak_unduh === true) {
                                // Munculkan tombol unduh, hilangkan tanda gembok
                                btnDownload.style.display = 'inline-block'; 
                                btnLocked.style.display = 'none';
                            } else {
                                // Hilangkan tombol unduh, munculkan tanda gembok
                                btnDownload.style.display = 'none'; 
                                btnLocked.style.display = 'block';
                            }
                        }
                    }
                    
                })
                .catch(error => console.error('Radar Error:', error));
            }, 3000); // Mengecek setiap 3 detik
        }
    });
</script>
@endpush