@extends('layouts.app')

@push('styles')
@section('header_title', 'Permintaan Akses Dokumen ')

<style>
    /* =========================================
         GAYA BANNER HEADER (TERANG & GELAP) 
       ========================================= */
    .banner-header {
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f59e0b, #ea580c); 
        color: #ffffff;
    }
    
    body.dark-mode .banner-header {
        background: linear-gradient(135deg, #422006, #0b1120); 
    }

    .banner-header .watermark-icon {
        position: absolute;
        right: -10px;
        bottom: -25px;
        font-size: 140px;
        opacity: 0.15;
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
    .verifikasi-card {
        border-radius: 16px;
        background-color: #ffffff;
        overflow: hidden;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
        vertical-align: middle;
    }

    .empty-state-icon { color: #cbd5e1; }

    /* --- KONFIGURASI DARK MODE UNTUK TABEL --- */
    body.dark-mode .verifikasi-card { background-color: #1e293b !important; border: 1px solid #334155 !important; }
    body.dark-mode .table, body.dark-mode .table > tbody, body.dark-mode .table > tbody > tr, body.dark-mode .table > tbody > tr > td { background-color: transparent !important; color: #f8fafc !important; border-bottom-color: #334155 !important; box-shadow: none !important; }
    body.dark-mode .table-light, body.dark-mode .table-light th, body.dark-mode .table > thead > tr > th { background-color: #0f172a !important; color: #94a3b8 !important; border-bottom: 2px solid #334155 !important; }
    body.dark-mode .table-hover > tbody > tr:hover > td { background-color: #0b1120 !important; color: #f8fafc !important; }
    body.dark-mode .text-muted { color: #94a3b8 !important; }
    body.dark-mode .text-dark { color: #f8fafc !important; }
    body.dark-mode .empty-state-icon { color: #475569 !important; }

    /* =========================================
        UI COMPACT UNTUK AKSI (ANTI-BERANTAKAN) 
       ========================================= */
    
    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        white-space: nowrap; 
    }

    .toggle-permission {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px; 
        padding: 0 10px;
        height: 32px; 
        display: inline-flex;
        align-items: center;
        transition: 0.3s;
    }
    .toggle-permission:hover { border-color: #cbd5e1; background: #f1f5f9; }
    .toggle-permission .form-switch { margin: 0; padding-left: 0; display: flex; align-items: center; gap: 8px; }
    .toggle-permission .form-check-input { margin: 0; cursor: pointer; width: 28px; height: 16px; }
    .toggle-permission .form-check-input:checked { background-color: #0284c7; border-color: #0284c7; }
    .toggle-permission label { font-size: 11px; font-weight: 700; cursor: pointer; margin: 0; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .btn-action-verify {
        height: 32px; 
        padding: 0 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-v-update { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .btn-v-update:hover { background: #16a34a; color: #fff; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2); transform: translateY(-1px); }
    .btn-v-cancel { background: #fff7ed; color: #d97706; border-color: #fde68a; }
    .btn-v-cancel:hover { background: #d97706; color: #fff; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2); transform: translateY(-1px); }
    .btn-v-restore { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }
    .btn-v-restore:hover { background: #0284c7; color: #fff; box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2); transform: translateY(-1px); }
    .btn-v-delete { background: #fef2f2; color: #e11d48; border-color: #fecdd3; padding: 0 10px; }
    .btn-v-delete:hover { background: #e11d48; color: #fff; box-shadow: 0 4px 10px rgba(225, 29, 72, 0.2); transform: translateY(-1px); }

    /*  PERBAIKAN: Warna Boleh Unduh Mode Terang  */
    .badge-soft-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

    body.dark-mode .toggle-permission { background: #0f172a; border-color: #334155; }
    body.dark-mode .toggle-permission:hover { border-color: #475569; }
    body.dark-mode .toggle-permission label { color: #94a3b8; }
    body.dark-mode .btn-v-update { background: rgba(22, 163, 74, 0.1); border-color: rgba(22, 163, 74, 0.2); color: #4ade80; }
    body.dark-mode .btn-v-update:hover { background: #16a34a; color: #fff; }
    body.dark-mode .btn-v-cancel { background: rgba(217, 119, 6, 0.1); border-color: rgba(217, 119, 6, 0.2); color: #fbbf24; }
    body.dark-mode .btn-v-cancel:hover { background: #d97706; color: #fff; }
    body.dark-mode .btn-v-restore { background: rgba(2, 132, 199, 0.1); border-color: rgba(2, 132, 199, 0.2); color: #38bdf8; }
    body.dark-mode .btn-v-restore:hover { background: #0284c7; color: #fff; }
    body.dark-mode .btn-v-delete { background: rgba(225, 29, 72, 0.1); border-color: rgba(225, 29, 72, 0.2); color: #fb7185; }
    body.dark-mode .btn-v-delete:hover { background: #e11d48; color: #fff; }
    body.dark-mode .badge-soft-info { background: rgba(2, 132, 199, 0.15); color: #7dd3fc; border-color: rgba(2, 132, 199, 0.3); }

    /* =========================================
        GAYA PAGINASI CUSTOM (TERPISAH & ROUNDED) 
       ========================================= */
    .pagination-wrapper {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background-color: transparent;
    }

    .pagination {
        gap: 6px; 
        margin: 0;
        flex-wrap: wrap;
    }

    .pagination .page-item .page-link {
        margin-left: 0 !important; 
        border-radius: 8px !important; 
        padding: 8px 15px;
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .pagination .page-item .page-link:hover {
        background-color: rgba(200, 163, 90, 0.1);
        color: #C8A35A;
        border-color: #C8A35A;
        transform: translateY(-2px);
    }

    .pagination .page-item.active .page-link {
        background-color: #C8A35A;
        border-color: #C8A35A;
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(200, 163, 90, 0.3);
        transform: translateY(-2px);
    }

    .pagination .page-item.disabled .page-link {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        box-shadow: none;
    }

    /* --- DARK MODE UNTUK PAGINASI --- */
    body.dark-mode .pagination-wrapper { border-top-color: #334155; }
    body.dark-mode .pagination .page-item .page-link { background-color: #1e293b !important; color: #cbd5e1 !important; border-color: #334155 !important; }
    body.dark-mode .pagination .page-item .page-link:hover { background-color: #334155 !important; color: #C8A35A !important; border-color: #C8A35A !important; }
    body.dark-mode .pagination .page-item.active .page-link { background-color: #C8A35A !important; color: #ffffff !important; border-color: #C8A35A !important; box-shadow: 0 4px 15px rgba(200, 163, 90, 0.25) !important; }
    body.dark-mode .pagination .page-item.disabled .page-link { background-color: #0f172a !important; color: #475569 !important; border-color: #1e293b !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    @include('partials.alerts') 
    
    <div class="card border-0 mb-4 shadow-sm banner-header">
        <div class="card-body p-4 p-md-5">
            <div class="banner-content">
                <h3 class="fw-bold mb-2 text-white">
                    <i class="fa-solid fa-shield-halved me-2"></i> Pusat Verifikasi Akses
                </h3>
                <p class="mb-0" style="max-width: 700px; opacity: 0.9; font-size: 15px; line-height: 1.6;">
                    Seluruh permohonan akses dokumen oleh pegawai maupun tamu akan tampil di sini. Anda dapat mengubah pengaturan hak unduh file asli atau membuang riwayat akses yang sudah tidak diperlukan.
                </p>
            </div>
            <i class="fa-solid fa-file-shield watermark-icon"></i>
        </div>
    </div>
    
    <div class="card shadow-sm border-0 verifikasi-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-muted" style="width: 20%; font-size: 11px; letter-spacing: 0.5px;">Identitas Pemohon</th>
                        <th class="text-uppercase text-muted" style="width: 25%; font-size: 11px; letter-spacing: 0.5px;">Dokumen Diminta</th>
                        <th class="text-uppercase text-muted" style="width: 15%; font-size: 11px; letter-spacing: 0.5px;">Waktu Minta</th>
                        <th class="text-uppercase text-muted" style="width: 15%; font-size: 11px; letter-spacing: 0.5px;">Status Saat Ini</th>
                        <th class="text-uppercase text-muted text-end pe-4" style="width: 25%; font-size: 11px; letter-spacing: 0.5px;">Aksi Verifikasi & Kelola</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaan as $item)
                    <tr>
                        <td>
                            @if($item->user)
                                <div class="fw-bold text-dark" style="font-size: 13px; line-height: 1.2; white-space: nowrap;">
                                    {{ $item->user->name }}
                                </div>
                                <div class="text-muted" style="font-size: 11px; margin-top: 4px; white-space: nowrap;">
                                    Pegawai Internal
                                </div>
                            @else
                                @php
                                    $infoParts = explode(' | ', $item->device_info);
                                    $namaTamu = isset($infoParts[1]) ? $infoParts[0] : 'Tamu Tidak Dikenal';
                                    $deviceTamu = isset($infoParts[1]) ? $infoParts[1] : $infoParts[0];
                                @endphp
                                <div class="fw-bold text-dark" style="font-size: 13px; line-height: 1.2; white-space: nowrap;">
                                    {{ $namaTamu }}
                                </div>
                                <div class="text-muted" style="font-size: 11px; margin-top: 4px; white-space: nowrap;">
                                    {{ $deviceTamu }}
                                </div>
                            @endif
                        </td>
                        
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ $item->arsip->kode_arsip ?? '-' }}</div>
                            <div class="text-muted" style="font-size: 11px; white-space: normal; max-width: 250px; line-height: 1.4;">
                                {{ $item->arsip->nama_berkas ?? 'Dokumen Dihapus' }}
                            </div>
                        </td>
                        
                        <td class="text-muted" style="font-size: 12px; white-space: nowrap;">
                            <i class="fa-regular fa-clock me-1"></i> {{ $item->created_at->diffForHumans() }}
                        </td>
                        
                        <td style="white-space: nowrap;">
                            @if($item->status == 'menunggu')
                                <span class="badge bg-warning text-dark px-2 py-1 shadow-sm"><i class="fa-solid fa-hourglass-half m-0"></i> Menunggu</span>
                            @elseif($item->status == 'disetujui')
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <span class="badge bg-success px-2 py-1 shadow-sm"><i class="fa-solid fa-check me-1"></i> Disetujui</span>
                                    @if($item->hak_unduh)
                                        <span class="badge badge-soft-info px-2 py-1" style="font-size: 10px;">
                                            <i class="fa-solid fa-cloud-arrow-down"></i> Boleh Unduh
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-danger px-2 py-1 shadow-sm"><i class="fa-solid fa-xmark me-1"></i> Ditolak</span>
                            @endif
                        </td>
                        
                        <td class="pe-4">
                            <div class="action-wrapper">
                                
                                <form action="{{ route('admin.verifikasi_izin', $item->id) }}" method="POST" class="m-0 d-flex align-items-center gap-2">
                                    @csrf @method('PUT')
                                    
                                    <div class="toggle-permission" title="Izinkan pegawai mengunduh file asli">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="hak_unduh" value="1" id="hakUnduh{{ $item->id }}" {{ $item->hak_unduh ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hakUnduh{{ $item->id }}">Unduh</label>
                                        </div>
                                    </div>

                                    @if($item->status == 'disetujui')
                                        <button type="submit" name="status" value="disetujui" class="btn-action-verify btn-v-update" title="Simpan pengaturan hak unduh">
                                            <i class="fa-solid fa-arrows-rotate"></i> Perbarui
                                        </button>
                                        <button type="submit" name="status" value="ditolak" class="btn-action-verify btn-v-cancel" title="Cabut akses pegawai">
                                            <i class="fa-solid fa-ban"></i> Cabut
                                        </button>
                                    @elseif($item->status == 'ditolak')
                                        <button type="submit" name="status" value="disetujui" class="btn-action-verify btn-v-restore" title="Pulihkan & setujui kembali">
                                            <i class="fa-solid fa-rotate-left"></i> Pulihkan
                                        </button>
                                    @else
                                        <button type="submit" name="status" value="disetujui" class="btn-action-verify btn-v-update" title="Setujui akses">
                                            <i class="fa-solid fa-check"></i> Setuju
                                        </button>
                                        <button type="submit" name="status" value="ditolak" class="btn-action-verify btn-v-cancel" title="Tolak akses">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    @endif
                                </form>

                                <form action="{{ route('admin.izin.destroy', $item->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus data permohonan ini secara permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action-verify btn-v-delete" title="Hapus Permanen">
                                        <i class="fa-solid fa-trash-can m-0"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fa-solid fa-inbox mb-3 empty-state-icon" style="font-size: 45px;"></i><br>
                            <span class="fw-bold text-dark" style="font-size: 16px;">Tidak ada antrean</span><br>
                            <span class="text-muted small">Belum ada permintaan akses dokumen saat ini.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($permintaan->hasPages())
        <div class="pagination-wrapper d-flex justify-content-end">
            {{ $permintaan->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection