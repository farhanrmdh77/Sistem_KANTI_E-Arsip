@extends('layouts.app')

@section('title', 'Hasil Pencarian: ' . $keyword . ' - ' . \App\Models\Setting::getAppName())

{{-- Kosongkan header_title agar tidak double dengan desain kustom kita di bawah --}}
@section('header_title', '') 

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    .search-page-wrapper { font-family: 'Poppins', sans-serif; color: #334155; padding-bottom: 50px; }

    /* Tombol Kembali */
    .btn-back-kustom { background: #ffffff; border: 1px solid #cbd5e1; color: #334155; border-radius: 50px; padding: 8px 20px; font-weight: 600; font-size: 14px; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.02); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-back-kustom:hover { background: #f8fafc; color: #0f172a; border-color: #94a3b8; transform: translateY(-2px); }

    /* Badge Keyword */
    .keyword-pill { background: rgba(200, 163, 90, 0.1); border: 1px dashed rgba(200, 163, 90, 0.4); color: #b48529; font-weight: 700; padding: 6px 16px; border-radius: 50px; font-size: 14px; }

    /* Main Container */
    .main-list-container { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 5px 20px rgba(0,0,0,0.02); padding: 25px 30px; margin-top: 25px; }

    /* Header Tabel Kustom */
    .custom-th { color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; margin-bottom: 15px; }

    /* Baris Dokumen (Card) */
    .doc-row-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 10px; margin-bottom: 12px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.01); }
    .doc-row-card:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(0,0,0,0.06); border-color: #C8A35A; z-index: 2; position: relative; }

    /* Teks Dokumen */
    .text-no { font-size: 16px; font-weight: 800; color: #475569; }
    .doc-title { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px; line-height: 1.3; }
    .doc-desc { font-size: 12px; color: #64748b; margin-bottom: 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .doc-year { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }

    /* Badges Identitas (Sesuai Gambar) */
    .b-kp { background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 11px; display: inline-flex; align-items: center; gap: 6px; }
    .b-kp i { color: #C8A35A; }
    
    .b-aktif { background: #ecfdf5; border: 1px solid #a7f3d0; color: #10b981; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 4px;}
    .b-inaktif { background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 4px;}
    .b-internal { background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
    
    .b-file-ada { background: #f0fdf4; border: 1px dashed #86efac; color: #16a34a; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;}
    .b-file-kosong { background: #f8fafc; border: 1px dashed #cbd5e1; color: #64748b; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }

    /* Tombol Aksi Jejer Rapi */
    .btn-act { padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 11px; display: inline-flex; align-items: center; gap: 5px; border: 1px solid transparent; transition: 0.2s; text-decoration: none; cursor: pointer; background: white;}
    .btn-act.icon-only { padding: 6px 10px; }
    
    .a-detail { background: #f0f9ff; color: #0284c7; border-color: #e0f2fe; }
    .a-detail:hover { background: #0284c7; color: white; transform: translateY(-2px); }
    .a-qr { background: #fdf4ff; color: #a855f7; border-color: #f3e8ff; }
    .a-qr:hover { background: #9333ea; color: white; transform: translateY(-2px); }
    .a-kp { background: #fffbeb; color: #d97706; border-color: #fef08a; }
    .a-kp:hover { background: #d97706; color: white; transform: translateY(-2px); }
    .a-edit { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .a-edit:hover { background: #475569; color: white; transform: translateY(-2px); }
    .a-delete { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
    .a-delete:hover { background: #ef4444; color: white; transform: translateY(-2px); }

    /* ========================================================= */
    /* 🌟 MODE GELAP (DARK MODE) 🌟                              */
    /* ========================================================= */
    body.dark-mode .search-page-wrapper { color: #cbd5e1; }
    body.dark-mode h2.text-dark, body.dark-mode h5.text-dark { color: #ffffff !important; }
    body.dark-mode .text-muted { color: #94a3b8 !important; }
    
    body.dark-mode .btn-back-kustom { background: #1e293b; color: #cbd5e1; border-color: #334155; }
    body.dark-mode .btn-back-kustom:hover { background: #0f172a; color: #fde68a; border-color: #C8A35A; }
    
    body.dark-mode .keyword-pill { background: rgba(200, 163, 90, 0.15); color: #fde68a; border-color: rgba(200, 163, 90, 0.4); }

    body.dark-mode .main-list-container { background: #0f172a; border-color: #1e293b; }
    body.dark-mode .custom-th { border-bottom-color: #334155; color: #cbd5e1; }
    
    body.dark-mode .doc-row-card { background: #1e293b; border-color: #334155; }
    body.dark-mode .doc-row-card:hover { border-color: #C8A35A; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
    
    body.dark-mode .text-no { color: #cbd5e1; }
    body.dark-mode .doc-title { color: #ffffff; }
    body.dark-mode .doc-year { color: #ffffff; }
    body.dark-mode .doc-desc { color: #94a3b8; }
    
    body.dark-mode .b-kp { background: #0f172a; border-color: #334155; color: #fde68a; }
    body.dark-mode .b-aktif { background: #064e3b; border-color: #065f46; color: #34d399; }
    body.dark-mode .b-inaktif { background: #7f1d1d; border-color: #991b1b; color: #fca5a5; }
    body.dark-mode .b-internal { background: #0f172a; border-color: #334155; color: #cbd5e1; }
    body.dark-mode .b-file-ada { background: #064e3b; border-color: #065f46; color: #34d399; }
    body.dark-mode .b-file-kosong { background: #0f172a; border-color: #334155; color: #94a3b8; }

    body.dark-mode .a-detail { background: rgba(2, 132, 199, 0.1); color: #38bdf8; border-color: rgba(2, 132, 199, 0.2); }
    body.dark-mode .a-detail:hover { background: #0284c7; color: white; }
    body.dark-mode .a-qr { background: rgba(168, 85, 247, 0.1); color: #c084fc; border-color: rgba(168, 85, 247, 0.2); }
    body.dark-mode .a-qr:hover { background: #9333ea; color: white; }
    body.dark-mode .a-kp { background: rgba(217, 119, 6, 0.1); color: #fbbf24; border-color: rgba(217, 119, 6, 0.2); }
    body.dark-mode .a-kp:hover { background: #d97706; color: white; }
    body.dark-mode .a-edit { background: rgba(71, 85, 105, 0.1); color: #94a3b8; border-color: rgba(71, 85, 105, 0.2); }
    body.dark-mode .a-edit:hover { background: #475569; color: white; }
    body.dark-mode .a-delete { background: rgba(239, 68, 68, 0.1); color: #fb7185; border-color: rgba(239, 68, 68, 0.2); }
    body.dark-mode .a-delete:hover { background: #e11d48; color: white; }

    /* Modals Override */
    body.dark-mode .modal-content { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .modal-content .bg-light { background: #1e293b !important; }
    body.dark-mode .detail-item { border-bottom: 1px solid #334155 !important; }
    body.dark-mode .detail-value { color: #f8fafc !important; }
    body.dark-mode .file-box-kosong { border-color: #334155 !important; }
</style>
@endpush

@section('content')
<div class="search-page-wrapper container-fluid pt-3">
    
    {{-- 🌟 HEADER HALAMAN (Persis seperti gambar image_1160c5) 🌟 --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2" style="font-size: 26px;">
                <i class="fa-solid fa-magnifying-glass" style="color: #C8A35A;"></i> Temuan Pencarian
            </h2>
            <p class="text-muted mb-0" style="font-size: 14px;">
                Menampilkan hasil arsip untuk kata kunci: 
                <span class="keyword-pill ms-1">"{{ $keyword }}"</span>
            </p>
        </div>
        <a href="{{ route('arsip.dashboard') }}" class="btn-back-kustom">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- 🌟 KOTAK DAFTAR DOKUMEN 🌟 --}}
    <div class="main-list-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
            <h5 class="fw-bold text-dark mb-0 fs-5"><i class="fa-solid fa-list-check me-2" style="color: #C8A35A;"></i> Daftar Dokumen Ditemukan</h5>
            <div class="keyword-pill border-0" style="font-size: 13px;">
                {{ count($arsips) }} Berkas Tersortir
            </div>
        </div>

        {{-- Jika Data Kosong --}}
        @if(count($arsips) == 0)
            <div class="text-center py-5">
                <i class="fa-solid fa-magnifying-glass-minus text-muted" style="font-size: 60px; opacity: 0.3; margin-bottom: 20px;"></i>
                <h4 class="fw-bold text-dark">Arsip Tidak Ditemukan</h4>
                <p class="text-muted">Kami tidak menemukan arsip fisik maupun digital yang cocok dengan kata kunci tersebut.</p>
            </div>
        @else

            {{-- HEADER GRID (Hanya Tampil di Desktop) --}}
            <div class="row custom-th d-none d-lg-flex mx-0 px-2">
                <div class="col-lg-1 text-center">NO</div>
                <div class="col-lg-3">INFORMASI DOKUMEN</div>
                <div class="col-lg-2">TAHUN & RETENSI</div>
                <div class="col-lg-2 text-center">KODE KP</div>
                <div class="col-lg-1 text-center">STATUS FILE</div>
                <div class="col-lg-3 text-center">AKSI</div>
            </div>

            {{-- LIST DOKUMEN --}}
            @foreach($arsips as $index => $arsip)
                @php
                    $currentYear = (int)date('Y');
                    $rawTahun = trim($arsip->tahun_berkas);
                    if (is_numeric($rawTahun) && $rawTahun > 30000) {
                        $tahunSistem = (int)date('Y', (($rawTahun - 25569) * 86400));
                        $displayTahun = $tahunSistem;
                    } else {
                        preg_match('/\d{4}/', $rawTahun, $matches);
                        $tahunSistem = !empty($matches) ? (int)$matches[0] : (int)date('Y');
                        $displayTahun = $rawTahun; 
                    }
                    $retensiAktif = (int)($arsip->retensi_aktif ?? 0);
                    $batasAktif = $tahunSistem + $retensiAktif;
                    $isInaktif = $currentYear > $batasAktif;
                @endphp

                <div class="row align-items-center doc-row-card mx-0" style="animation: fadeUpRow 0.5s ease forwards; animation-delay: {{ $index * 0.05 }}s; opacity: 0;">
                    
                    {{-- 1. NO --}}
                    <div class="col-12 col-lg-1 text-center mb-2 mb-lg-0">
                        <span class="text-no">{{ $index + 1 }}</span>
                    </div>

                    {{-- 2. INFORMASI DOKUMEN --}}
                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 pe-lg-4">
                        <div class="doc-title">{{ $arsip->nama_berkas }}</div>
                        <div class="doc-desc">{{ $arsip->deskripsi_berkas ?: 'Tidak ada deskripsi tambahan.' }}</div>
                    </div>

                    {{-- 3. TAHUN & RETENSI --}}
                    <div class="col-12 col-lg-2 mb-3 mb-lg-0 text-center text-lg-start">
                        <div class="doc-year">{{ $displayTahun }}</div>
                        @if(!$isInaktif)
                            <div class="b-aktif"><i class="fa-solid fa-shield-check"></i> Aktif</div>
                        @else
                            <div class="b-inaktif"><i class="fa-solid fa-fire"></i> Inaktif (Musnah)</div>
                        @endif
                        <br>
                        <div class="b-internal"><i class="fa-solid fa-box-archive"></i> Internal</div>
                    </div>

                    {{-- 4. KODE KP --}}
                    <div class="col-12 col-lg-2 mb-3 mb-lg-0 text-center">
                        <div class="b-kp"><i class="fa-solid fa-tag"></i> {{ $arsip->kode_arsip }}</div>
                    </div>

                    {{-- 5. STATUS FILE --}}
                    <div class="col-12 col-lg-1 mb-3 mb-lg-0 text-center">
                        @if($arsip->file_dokumen)
                            <a href="{{ asset('storage/' . $arsip->file_dokumen) }}" target="_blank" class="b-file-ada" title="Lihat/Buka PDF"><i class="fa-solid fa-file-circle-check"></i> Ada</a>
                        @else
                            <div class="b-file-kosong"><i class="fa-solid fa-file-circle-xmark"></i> Kosong</div>
                        @endif
                    </div>

                    {{-- 6. AKSI --}}
                    <div class="col-12 col-lg-3 text-center">
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <button type="button" class="btn-act a-detail" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $arsip->id }}">
                                <i class="fa-solid fa-eye"></i> Detail
                            </button>

                            @auth
                            <button type="button" class="btn-act a-qr" data-bs-toggle="modal" data-bs-target="#modalQR{{ $arsip->id }}">
                                <i class="fa-solid fa-qrcode"></i> Scan QR
                            </button>

                            @can('admin')
                            <button type="button" class="btn-act a-kp" data-bs-toggle="modal" data-bs-target="#modalEditKP{{ $arsip->id }}">
                                <i class="fa-solid fa-tags"></i> Edit KP
                            </button>
                            <a href="{{ route('arsip.edit', $arsip->id) }}" class="btn-act icon-only a-edit" title="Edit Full">
                                <i class="fa-solid fa-pen-clip"></i>
                            </a>
                            <button type="button" class="btn-act icon-only a-delete" title="Hapus" onclick="confirmSingleDelete({{ $arsip->id }})">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            @endcan
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    {{-- 🌟 AREA MODALS (Diletakkan Paling Bawah Anti-Bentrok) 🌟 --}}
    @foreach($arsips as $arsip)
        {{-- MODAL DETAIL DOKUMEN --}}
        <div class="modal fade" id="modalDetail{{ $arsip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header border-0 p-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-bottom: 3px solid #C8A35A !important;">
                        <h5 class="modal-title fw-bold" style="letter-spacing: 0.5px; font-size: 15px;">
                            <i class="fa-solid fa-magnifying-glass-chart me-2" style="color: #C8A35A;"></i> Rincian Arsip
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <div class="col-md-5 p-4 bg-light" style="border-right: 1px solid #e2e8f0;">
                                <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-tags text-muted me-1"></i> Identitas Dokumen</h6>
                                <div class="d-flex flex-column gap-1">
                                    <div class="detail-item pb-2 mb-2" style="border-bottom: 1px solid #e2e8f0;">
                                        <span class="d-block text-muted small fw-bold">Kode KP</span>
                                        <span class="detail-value fw-bold" style="color: #C8A35A;">{{ $arsip->kode_arsip }}</span>
                                    </div>
                                    <div class="detail-item pb-2 mb-2" style="border-bottom: 1px solid #e2e8f0;">
                                        <span class="d-block text-muted small fw-bold">Tahun</span>
                                        <span class="detail-value fw-bold">{{ $arsip->tahun_berkas }}</span>
                                    </div>
                                    <div class="detail-item pb-2 mb-2" style="border-bottom: 1px solid #e2e8f0;">
                                        <span class="d-block text-muted small fw-bold">Warna Berkas</span>
                                        <span class="detail-value">{{ $arsip->warna_berkas ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 p-4 d-flex flex-column">
                                <div class="mb-3">
                                    <span style="font-size: 11px; font-weight: 700; color: #C8A35A; text-transform: uppercase;">Judul Berkas</span>
                                    <h5 class="fw-bold text-dark mt-1 mb-0" style="line-height: 1.4; font-size: 16px;">{{ $arsip->nama_berkas }}</h5>
                                </div>
                                <div class="mb-auto">
                                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Deskripsi / Uraian</span>
                                    <div class="mt-1 text-muted" style="font-size: 12px; line-height: 1.6;">
                                        {!! $arsip->deskripsi_berkas ? nl2br(e($arsip->deskripsi_berkas)) : '<span class="fst-italic">Tidak ada uraian.</span>' !!}
                                    </div>
                                </div>
                                <div class="mt-3 p-3 rounded-3 border file-box-kosong">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div style="font-size: 11px; font-weight: 700; margin-bottom: 2px;"><i class="fa-solid fa-paperclip"></i> File Digital</div>
                                            <div style="font-size: 12px; font-weight: 600;" class="detail-value text-dark">{{ $arsip->file_dokumen ? 'Tersedia di Server' : 'Kosong' }}</div>
                                        </div>
                                        @if($arsip->file_dokumen)
                                            <a href="{{ asset('storage/' . $arsip->file_dokumen) }}" download class="btn btn-sm btn-success fw-bold px-3 rounded-pill" style="font-size: 11px;">
                                                <i class="fa-solid fa-download"></i> Unduh
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL QR CODE --}}
        @auth
        <div class="modal fade" id="modalQR{{ $arsip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-0 p-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-bottom: 3px solid #C8A35A !important;">
                        <h6 class="modal-title fw-bold" style="font-size: 14px;"><i class="fa-solid fa-qrcode text-warning"></i> Scan QR Akses</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <div class="bg-white p-2 d-inline-block rounded shadow-sm border mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('qr.scan', $arsip->id)) }}" alt="QR Code" width="150">
                        </div>
                        <h6 class="fw-bold text-dark" style="font-size: 13px;">{{ $arsip->kode_arsip }}</h6>
                        <p class="text-muted small mb-0">Arahkan kamera HP ke QR Code ini untuk meminta izin akses.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL UBAH KODE KLASIFIKASI (KP) CEPAT --}}
        @can('admin')
        <div class="modal fade" id="modalEditKP{{ $arsip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-0 p-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-bottom: 3px solid #ca8a04 !important;">
                        <h6 class="modal-title fw-bold" style="font-size: 14px;"><i class="fa-solid fa-tags text-warning"></i> Ubah Klasifikasi</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('arsip.update_kp', $arsip->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Kode Saat Ini</label>
                                <div class="fw-bold text-dark p-2 border rounded text-center bg-light">{{ $arsip->kode_arsip }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Kode Baru <span class="text-danger">*</span></label>
                                <input type="text" name="kode_arsip" class="form-control form-control-sm text-center fw-bold" value="{{ $arsip->kode_arsip }}" required placeholder="Contoh: KP.15.01">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                            <button type="button" class="btn btn-light btn-sm fw-bold w-100 mb-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm w-100 fw-bold border-0 shadow-sm" style="background: #eab308; color: #fff;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan
        @endauth
    @endforeach
    
    @auth
    @can('admin')
    <form id="singleDeleteForm" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    @endcan
    @endauth

</div>

<style>
    @keyframes fadeUpRow { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSingleDelete(id) {
        Swal.fire({
            title: 'Pindahkan Arsip?',
            text: "Dokumen ini akan dipindahkan sementara ke Tong Sampah.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#475569',
            confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Pindahkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: `rgba(15, 23, 42, 0.4)`,
            customClass: { popup: 'border border-light shadow-lg', title: 'fs-4 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('singleDeleteForm');
                form.action = `/arsip/${id}`; 
                form.submit();
            }
        });
    }
</script>
@endpush