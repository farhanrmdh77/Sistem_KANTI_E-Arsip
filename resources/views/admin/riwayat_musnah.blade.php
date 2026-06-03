@extends('layouts.app')

@section('title', 'Riwayat Pemusnahan - ' . \App\Models\Setting::getAppName())
@section('header_title', 'Audit Pemusnahan Arsip')

@push('styles')
<style>
    /* =========================================
        GAYA BANNER HEADER (TERANG & GELAP) 
       ========================================= */
    .banner-header {
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        /* Menggunakan gradasi Slate/Vault untuk kesan Arsip Lama */
        background: linear-gradient(135deg, #334155, #0f172a); 
        color: #ffffff;
    }
    
    body.dark-mode .banner-header {
        background: linear-gradient(135deg, #1e293b, #020617); 
    }

    .banner-header .watermark-icon {
        position: absolute;
        right: -10px;
        bottom: -25px;
        font-size: 140px;
        opacity: 0.1;
        color: #ffffff;
        transform: rotate(-15deg);
        z-index: 0;
        pointer-events: none;
    }

    .banner-header .banner-content {
        position: relative;
        z-index: 1;
    }

    /* =========================================
        GAYA TABEL & CARD DATA
       ========================================= */
    .table-card {
        border-radius: 16px;
        background-color: #ffffff;
        overflow: hidden;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 1.2rem;
        vertical-align: middle;
    }

    .empty-state-icon { color: #cbd5e1; }

    /* --- KONFIGURASI DARK MODE UNTUK TABEL --- */
    body.dark-mode .table-card { background-color: #1e293b !important; border: 1px solid #334155 !important; }
    body.dark-mode .table, body.dark-mode .table > tbody, body.dark-mode .table > tbody > tr, body.dark-mode .table > tbody > tr > td { background-color: transparent !important; color: #f8fafc !important; border-bottom-color: #334155 !important; box-shadow: none !important; }
    body.dark-mode .table-light, body.dark-mode .table-light th, body.dark-mode .table > thead > tr > th { background-color: #0f172a !important; color: #94a3b8 !important; border-bottom: 2px solid #334155 !important; }
    body.dark-mode .table-hover > tbody > tr:hover > td { background-color: #0b1120 !important; color: #f8fafc !important; }
    body.dark-mode .text-muted { color: #94a3b8 !important; }
    body.dark-mode .empty-state-icon { color: #475569 !important; }
    
    /* Memperbaiki warna nomor/teks muted di mode gelap */
    body.dark-mode .table td.col-no, body.dark-mode .table .text-muted { color: #cbd5e1 !important; }

    /* =========================================
        TOMBOL BAP (SOFT UI) 
       ========================================= */
    .btn-bap {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.3s ease;
        text-decoration: none;
        background: #fef2f2; 
        color: #e11d48; 
        border: 1px solid #fecdd3;
    }
    .btn-bap:hover { background: #e11d48; color: #fff; box-shadow: 0 4px 10px rgba(225, 29, 72, 0.2); transform: translateY(-1px); }

    body.dark-mode .btn-bap { background: rgba(225, 29, 72, 0.1); border-color: rgba(225, 29, 72, 0.2); color: #fb7185; }
    body.dark-mode .btn-bap:hover { background: #e11d48; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    {{-- Notifikasi --}}
    @include('partials.alerts') 
    
    {{-- Banner Header --}}
    <div class="card border-0 mb-4 shadow-sm banner-header">
        <div class="card-body p-4 p-md-5">
            <div class="banner-content">
                <h3 class="fw-bold mb-2 text-white">
                    <i class="fa-solid fa-file-shield me-2"></i> Rekam Jejak Pemusnahan
                </h3>
                <p class="mb-0" style="max-width: 700px; opacity: 0.9; font-size: 15px; line-height: 1.6;">
                    Daftar dokumen kearsipan yang telah dieksekusi musnah secara permanen dari sistem operasional. Anda dapat mengunduh ulang Berita Acara Pemusnahan (BAP) digital sebagai bukti audit kapan saja.
                </p>
            </div>
            <i class="fa-solid fa-clock-rotate-left watermark-icon"></i>
        </div>
    </div>
    
    {{-- Tabel Card --}}
    <div class="card shadow-sm border-0 table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-uppercase text-muted" style="width: 50px; font-size: 11px; letter-spacing: 0.5px;">No</th>
                        <th class="text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Identitas Dokumen</th>
                        <th class="text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Tanggal Eksekusi Musnah</th>
                        <th class="text-center text-uppercase text-muted" style="width: 150px; font-size: 11px; letter-spacing: 0.5px;">Bukti Digital</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $item)
                    <tr>
                        <td class="text-center text-muted fw-bold col-no" style="font-size: 13px;">
                            {{ $index + 1 }}
                        </td>
                        
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                <i class="fa-solid fa-folder-closed text-secondary me-1"></i> {{ $item->kode_arsip ?? 'Arsip' }}
                            </div>
                            <div class="text-muted" style="font-size: 12px; white-space: normal; max-width: 400px; line-height: 1.4;">
                                {{ $item->nama_berkas }}
                                @if($item->tahun_berkas)
                                    &bull; Tahun: {{ $item->tahun_berkas }}
                                @endif
                            </div>
                        </td>
                        
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                {{ $item->deleted_at->format('d F Y') }}
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                <i class="fa-regular fa-clock me-1"></i> {{ $item->deleted_at->format('H:i') }} WIB
                            </div>
                        </td>
                        
                        <td class="text-center">
                            @if($item->file_bap)
                                <a href="{{ asset('storage/' . $item->file_bap) }}" target="_blank" class="btn-bap" title="Buka Dokumen BAP PDF">
                                    <i class="fa-solid fa-file-pdf"></i> Lihat BAP
                                </a>
                            @else
                                <span class="badge bg-secondary px-2 py-1"><i class="fa-solid fa-link-slash"></i> BAP Hilang</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fa-solid fa-box-archive mb-3 empty-state-icon" style="font-size: 45px;"></i><br>
                            <span class="fw-bold text-dark" style="font-size: 16px;">Belum Ada Riwayat</span><br>
                            <span class="text-muted small">Sistem belum pernah melakukan pemusnahan dokumen arsip.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection