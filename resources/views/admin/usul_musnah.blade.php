@extends('layouts.app')

@section('title', 'Usul Pemusnahan Arsip - ' . \App\Models\Setting::getAppName())
@section('header_title', 'Verifikasi Pemusnahan')

@push('styles')
<style>
    /* =========================================
        GAYA BANNER HEADER (TERANG & GELAP) 
       ========================================= */
    .banner-header { border-radius: 16px; position: relative; overflow: hidden; background: linear-gradient(135deg, #ef4444, #991b1b); color: #ffffff; }
    body.dark-mode .banner-header { background: linear-gradient(135deg, #7f1d1d, #450a0a); }
    .banner-header .watermark-icon { position: absolute; right: -10px; bottom: -30px; font-size: 150px; opacity: 0.15; color: #ffffff; transform: rotate(-15deg); z-index: 0; pointer-events: none; }

    /* =========================================
        ARSITEKTUR EA-TABLE (RINGAN & ANTI-LAG)
       ========================================= */
    .table-card { background: #ffffff; border-radius: 20px; padding: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; }
    .table-card-header { border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 10px; }

    .ea-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .ea-table th { padding: 15px 20px; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: none; text-align: left; white-space: nowrap; }
    
    .ea-table tbody tr { background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.01); border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s; opacity: 0; animation: fadeUpRow 0.5s ease forwards; }
    @keyframes fadeUpRow { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .ea-table tbody tr:hover { transform: translateY(-2px) scale(1.005); box-shadow: 0 10px 25px rgba(0,0,0,0.04); z-index: 2; position: relative; }
    
    .ea-table td { padding: 15px 20px; font-size: 13px; color: #334155; vertical-align: middle; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .ea-table td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .ea-table td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .cb-custom { width: 18px; height: 18px; cursor: pointer; accent-color: #ef4444; border: 2px solid #cbd5e1; border-radius: 4px; transition: 0.2s; }
    
    .doc-title { font-weight: 700; color: #0f172a; font-size: 14px; margin-bottom: 4px; }
    .doc-desc { color: #64748b; font-size: 12px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal; }
    .badge-kp { background: #f8fafc; color: #0f172a; padding: 6px 10px; border-radius: 8px; font-weight: 700; font-size: 11px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px; }
    
    .btn-eksekusi { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; border: none; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); transition: 0.3s; }
    .btn-eksekusi:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); color: white; }

    .row-selected { background-color: #fef2f2 !important; }
    .row-selected td { border-top-color: #fecdd3 !important; border-bottom-color: #fecdd3 !important; }
    .row-selected td:first-child { border-left-color: #fecdd3 !important; }
    .row-selected td:last-child { border-right-color: #fecdd3 !important; }

    /* =========================================
        GAYA PENCARIAN & PAGINASI 
       ========================================= */
    .search-area { position: relative; width: 320px; margin: 0; }
    .search-input { width: 100%; padding: 10px 85px 10px 40px; border-radius: 50px; border: 1px solid #e2e8f0; font-size: 13px; color: #334155; background: #ffffff; transition: 0.3s; }
    .search-input:focus { border-color: #ef4444; outline: none; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #ef4444; font-size: 14px;}
    .btn-search { position: absolute; right: 5px; top: 5px; bottom: 5px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; border: none; border-radius: 40px; padding: 0 20px; font-weight: 600; font-size: 12px; transition: 0.3s; }
    .btn-search:hover { box-shadow: 0 3px 10px rgba(239, 68, 68, 0.3); }

    /*  PERBAIKAN PAGINASI: HILANGKAN TEKS GANDA & RAPIKAN  */
    .pagination-wrapper { margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .pagination-links nav { margin: 0; width: 100%; }
    .pagination-links nav > div.d-sm-none { display: none !important; } /* Sembunyikan navigasi mobile default */
    .pagination-links nav > div.d-none.d-sm-flex { display: flex !important; justify-content: flex-end !important; width: 100%; }
    .pagination-links nav > div.d-sm-flex > div:first-child { display: none !important; } /* 🔥 Sembunyikan "Showing X to Y" bawaan Laravel 🔥 */
    .pagination-links nav > div.d-sm-flex > div:last-child { margin: 0; }
    
    .pagination { gap: 6px; margin: 0; flex-wrap: wrap; }
    .pagination .page-item .page-link { margin-left: 0 !important; border-radius: 8px !important; padding: 8px 14px; font-weight: 600; font-size: 13px; color: #475569; background-color: #f8fafc; border: 1px solid #e2e8f0; transition: all 0.3s ease; }
    .pagination .page-item .page-link:hover { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: #ef4444; transform: translateY(-2px); }
    .pagination .page-item.active .page-link { background-color: #ef4444; border-color: #ef4444; color: #ffffff; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); transform: translateY(-2px); }

    /* --- KONFIGURASI DARK MODE --- */
    body.dark-mode .table-card { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .table-card-header { border-bottom-color: #1e293b !important; }
    body.dark-mode .ea-table th { color: #cbd5e1 !important; }
    body.dark-mode .ea-table td { border-top-color: #1e293b !important; border-bottom-color: #1e293b !important; }
    body.dark-mode .ea-table td:first-child { border-left-color: #1e293b !important; }
    body.dark-mode .ea-table td:last-child { border-right-color: #1e293b !important; }
    body.dark-mode .ea-table tbody tr { background-color: #0f172a !important; }
    body.dark-mode .ea-table tbody tr:hover { background-color: #1e293b !important; }
    body.dark-mode .doc-title { color: #ffffff !important; }
    body.dark-mode .doc-desc { color: #cbd5e1 !important; }
    body.dark-mode .badge-kp { background: #1e293b !important; color: #fde68a !important; border-color: #334155 !important; }
    body.dark-mode .badge.bg-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #fcd34d !important; border: 1px solid rgba(245, 158, 11, 0.3); }
    body.dark-mode .row-selected { background-color: rgba(239, 68, 68, 0.08) !important; }
    body.dark-mode .row-selected td { border-top-color: rgba(239, 68, 68, 0.2) !important; border-bottom-color: rgba(239, 68, 68, 0.2) !important; }
    body.dark-mode .search-input { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    body.dark-mode .search-input:focus { border-color: #ef4444 !important; }
    body.dark-mode .pagination-wrapper { border-top-color: #334155 !important; }
    body.dark-mode .pagination .page-item .page-link { background-color: #1e293b !important; color: #cbd5e1 !important; border-color: #334155 !important; }
    body.dark-mode .pagination .page-item .page-link:hover { background-color: #334155 !important; color: #ef4444 !important; border-color: #ef4444 !important; }
    body.dark-mode .pagination .page-item.active .page-link { background-color: #ef4444 !important; color: #ffffff !important; border-color: #ef4444 !important; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3) !important; }
    body.dark-mode .pagination .page-item.disabled .page-link { background-color: #0f172a !important; color: #475569 !important; border-color: #1e293b !important; }
    body.dark-mode .page-count-text { color: #f8fafc !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    @include('partials.alerts')

    <div class="card border-0 mb-4 shadow-sm banner-header">
        <div class="card-body p-4 p-md-5">
            <div class="banner-content">
                <h3 class="fw-bold mb-2 text-white">
                    <i class="fa-solid fa-fire-flame-curved me-2"></i> Daftar Usul Pemusnahan Arsip
                </h3>
                <p class="mb-0" style="max-width: 700px; opacity: 0.9; font-size: 15px; line-height: 1.6;">
                    Dokumen di bawah ini telah melewati batas masa retensi aktif dan inaktif berdasarkan sistem. Silakan verifikasi dan setujui untuk memusnahkan data ini demi efisiensi ruang server. Berita Acara Pemusnahan (BAP) akan dibuat otomatis.
                </p>
            </div>
            <i class="fa-solid fa-dumpster-fire watermark-icon"></i>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div class="fw-bold text-muted">
            <i class="fa-solid fa-filter"></i> Filter & Pencarian
        </div>
        <form action="{{ route('admin.usul_musnah') }}" method="GET" class="search-area ms-auto">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" name="search" class="search-input shadow-sm" placeholder="Cari nama berkas, tahun, atau kode..." value="{{ request('search') }}">
            <button type="submit" class="btn-search">Cari</button>
        </form>
    </div>

    <form action="{{ route('admin.proses_musnah') }}" method="POST" id="formMusnah">
        @csrf
        <div class="table-card">
            
            <div class="table-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h6 class="m-0 fw-bold d-flex align-items-center style-title">
                    <i class="fa-solid fa-list-check text-danger me-2" style="font-size: 18px;"></i> 
                    Menunggu Verifikasi Pimpinan
                </h6>
                <button type="button" class="btn btn-eksekusi fw-bold rounded-pill px-4" id="btnSubmitMusnah" disabled>
                    <i class="fa-solid fa-file-signature me-1"></i> Setuju Pemusnahan (<span id="selectedCount">0</span>)
                </button>
            </div>

            <div class="table-responsive">
                <table class="ea-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">
                                <input type="checkbox" id="checkAll" class="cb-custom" title="Pilih Semua">
                            </th>
                            <th style="width: 15%;">Kode KP</th>
                            <th style="width: 50%;">Informasi Berkas</th>
                            <th class="text-center" style="width: 10%;">Tahun</th>
                            <th class="text-center" style="width: 20%;">Status Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arsipUsul as $index => $arsip)
                        <tr style="animation-delay: {{ $index * 0.02 }}s;">
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $arsip->id }}" class="cb-custom check-item">
                            </td>
                            <td>
                                <span class="badge-kp">
                                    <i class="fa-solid fa-tag text-danger"></i> {{ $arsip->kode_arsip }}
                                </span>
                            </td>
                            <td>
                                <div style="min-width: 0;">
                                    <div class="doc-title">{{ $arsip->nama_berkas }}</div>
                                    <div class="doc-desc">{{ $arsip->deskripsi_berkas ?? 'Tidak ada deskripsi berkas.' }}</div>
                                </div>
                            </td>
                            <td class="text-center fw-bold" style="font-size: 14px;">
                                {{ $arsip->tahun_berkas }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark px-2 py-1 shadow-sm" style="font-size: 11px; white-space: nowrap;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Usul Musnah
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5" style="border: none; background: transparent;">
                                <div style="color: #cbd5e1; font-size: 50px; margin-bottom: 15px;"><i class="fa-solid fa-shield-check text-success"></i></div>
                                <h5 class="fw-bold">Sistem Bersih</h5>
                                <p class="text-muted small">Belum ada dokumen yang mencapai batas waktu pemusnahan (JRA).</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($arsipUsul, 'hasPages'))
            <div class="pagination-wrapper">
                <div class="text-muted small fw-medium" style="color: #94a3b8 !important;">
                    Menampilkan <b class="page-count-text">{{ $arsipUsul->firstItem() ?? 0 }}</b> hingga <b class="page-count-text">{{ $arsipUsul->lastItem() ?? 0 }}</b> dari total <b class="page-count-text">{{ $arsipUsul->total() }}</b> usulan.
                </div>
                
                <div class="pagination-links">
                    {{--  KUNCI: ->onEachSide(1) untuk membatasi jumlah kotak angka!  --}}
                    {{ $arsipUsul->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkAll = document.getElementById('checkAll');
        const checkItems = document.querySelectorAll('.check-item');
        const selectedCountText = document.getElementById('selectedCount');
        const btnSubmitMusnah = document.getElementById('btnSubmitMusnah');

        function updateSelection() {
            let checkedCount = 0;
            checkItems.forEach(item => {
                const tr = item.closest('tr');
                if (item.checked) {
                    tr.classList.add('row-selected');
                    checkedCount++;
                } else {
                    tr.classList.remove('row-selected');
                }
            });

            selectedCountText.innerText = checkedCount;

            if (checkedCount > 0) {
                btnSubmitMusnah.removeAttribute('disabled');
            } else {
                btnSubmitMusnah.setAttribute('disabled', 'disabled');
            }
            
            if(checkAll && checkItems.length > 0) {
                checkAll.checked = checkedCount === checkItems.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(item => item.checked = this.checked);
                updateSelection();
            });
        }

        checkItems.forEach(item => {
            item.addEventListener('change', updateSelection);
        });

        if (btnSubmitMusnah) {
            btnSubmitMusnah.addEventListener('click', function(e) {
                e.preventDefault();
                const count = selectedCountText.innerText;
                
                Swal.fire({
                    title: 'Musnahkan ' + count + ' Arsip?',
                    text: "Seluruh berkas fisik digital akan dihapus permanen dan Berita Acara (BAP) segera diterbitkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', 
                    cancelButtonColor: '#475569', 
                    confirmButtonText: '<i class="fa-solid fa-dumpster-fire me-1"></i> Ya, Eksekusi!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true, 
                    backdrop: `rgba(15, 23, 42, 0.4)`, 
                    customClass: { popup: 'border border-light shadow-lg', title: 'fs-4 fw-bold' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formMusnah').submit();
                    }
                });
            });
        }
    });
</script>
@endpush