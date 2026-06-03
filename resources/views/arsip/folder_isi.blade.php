@extends('layouts.app')

@section('title', 'Isi Folder ' . $kode . ' - ' . \App\Models\Setting::getAppName())
@section('header_title', 'Data Arsip Kategori: ' . $kode) 

@push('styles')
<style>
    .custom-page { font-family: 'Poppins', sans-serif; color: #334155; padding-top: 10px; padding-bottom: 50px; }
    
    .header-banner { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px; padding: 35px 40px; margin-bottom: 25px; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); position: relative; overflow: hidden; border: 1px solid rgba(200, 163, 90, 0.2); }
    .header-banner::after { content: '\f07b'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -20px; top: -40px; font-size: 200px; color: #C8A35A; opacity: 0.05; transform: rotate(-15deg); pointer-events: none; }
    .folder-info-wrapper { display: flex; align-items: center; gap: 20px; margin-bottom: 12px; position: relative; z-index: 2; }
    .folder-icon-box { width: 60px; height: 60px; background: linear-gradient(135deg, #C8A35A 0%, #ae8b49 100%); color: #ffffff; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: 28px; box-shadow: 0 10px 20px rgba(200, 163, 90, 0.3); }
    .folder-title { font-size: 28px; font-weight: 800; color: #ffffff; margin: 0; letter-spacing: 0.5px; }
    .kode-badge { background: rgba(255, 255, 255, 0.1); color: #C8A35A; padding: 6px 16px; border-radius: 50px; font-size: 15px; font-weight: 700; border: 1px solid rgba(200, 163, 90, 0.4); backdrop-filter: blur(5px); }
    .folder-desc { color: #94a3b8; font-size: 14px; font-weight: 400; margin-left: 80px; margin-bottom: 0; position: relative; z-index: 2; }

    .smart-action-bar { position: -webkit-sticky; position: sticky; top: 90px; z-index: 900; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px) saturate(180%); padding: 18px 25px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(200, 163, 90, 0.1); margin-bottom: 25px; display: flex; flex-direction: column; gap: 15px; transition: top 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s ease, border-color 0.3s ease; }
    .smart-action-bar.hide-up { top: -150px; opacity: 0; pointer-events: none; }
    .smart-action-bar.peek-down { top: 90px; opacity: 1; pointer-events: auto; }

    .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .btn-action { padding: 10px 20px; border-radius: 40px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; transition: all 0.3s ease; text-decoration: none; cursor: pointer; }
    .btn-pdf { background: #fff5f5; color: #e11d48; border-color: #fecdd3; }
    .btn-pdf:hover { background: #e11d48; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(225, 29, 72, 0.2); }
    .btn-add { background: #f0fdf4; color: #059669; border-color: #a7f3d0; }
    .btn-add:hover { background: #059669; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(5, 150, 105, 0.2); }
    .btn-back { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .btn-back:hover { background: #1e293b; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(30, 41, 59, 0.2); }

    .bottom-bar-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%; }
    .entries-capsule { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50px; padding: 4px 15px; display: inline-flex; align-items: center; gap: 8px; width: fit-content; transition: 0.3s; white-space: nowrap; }
    .entries-select { color: #1e293b; box-shadow: none !important; cursor: pointer; padding: 2px 25px 2px 5px; font-weight: 700; font-size: 13px; border: none; background-color: transparent; width: auto; }
    .entries-select:focus { outline: none; }
    .filter-select { min-width: 140px; } 

    .btn-mode-hapus { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 8px 20px; border-radius: 50px; font-weight: 600; font-size: 13px; transition: 0.3s; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
    .btn-mode-hapus:hover { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); transform: translateY(-2px); }
    .btn-mode-hapus.active { background: #1e293b; color: white; border-color: #1e293b; }

    .btn-bulk-delete { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; border: 1px solid transparent; padding: 8px 20px; border-radius: 50px; font-weight: 600; font-size: 13px; display: none; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); transition: 0.3s; white-space: nowrap; }
    .btn-bulk-delete:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); }
    .btn-bulk-delete.show-anim { display: inline-flex; animation: popIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    .search-area { position: relative; width: 320px; margin: 0; }
    .search-input { width: 100%; padding: 12px 90px 12px 45px; border-radius: 50px; border: 1px solid #e2e8f0; font-size: 13px; color: #334155; background: #ffffff; transition: 0.3s; }
    .search-input:focus { border-color: #C8A35A; outline: none; background: #ffffff; box-shadow: 0 0 0 3px rgba(200, 163, 90, 0.15); }
    .search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #C8A35A; font-size: 15px;}
    .btn-search { position: absolute; right: 5px; top: 5px; bottom: 5px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none; border-radius: 40px; padding: 0 20px; font-weight: 600; font-size: 12px; transition: 0.3s; }
    .btn-search:hover { background: #C8A35A; box-shadow: 0 3px 10px rgba(200, 163, 90, 0.3); }

    /* =========================================
       🗂️ TABEL ARSIP - OPTIMASI ANTI SCROLL & RAMPING
       ========================================= */
    .table-card { background: #ffffff; border-radius: 20px; padding: 10px 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; overflow-x: hidden; }
    .ea-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    
    .ea-table th { padding: 12px 10px; color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; border-bottom: none; text-align: left; white-space: nowrap; }
    .ea-table td { padding: 12px 10px; font-size: 13px; color: #334155; vertical-align: middle; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .ea-table td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .ea-table td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .cb-custom { width: 18px; height: 18px; cursor: pointer; accent-color: #ef4444; border: 2px solid #cbd5e1; border-radius: 4px; transition: 0.2s; margin-top: 3px;}
    .col-checkbox { text-align: center !important; padding-right: 5px !important; transition: 0.3s; }
    .col-no { text-align: center !important; } 
    .col-aksi { white-space: nowrap; text-align: center !important; } 

    .ea-table tbody tr { background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.01); border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s; opacity: 0; animation: fadeUpRow 0.5s ease forwards; }
    @keyframes fadeUpRow { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .ea-table tbody tr:hover { transform: translateY(-2px) scale(1.005); box-shadow: 0 10px 25px rgba(0,0,0,0.04); z-index: 2; position: relative; }
    .ea-table tbody tr.row-selected { background-color: #fef2f2 !important; border-left: 3px solid #ef4444; box-shadow: 0 5px 15px rgba(239, 68, 68, 0.1); }
    
    .ea-table tbody tr:target { background-color: #fffbeb !important; border-left: 5px solid #d97706 !important; box-shadow: 0 5px 20px rgba(217, 119, 6, 0.3) !important; z-index: 5; position: relative; opacity: 1 !important; transform: translateY(0) !important; }
    .row-target-blink { animation: pulseTarget 2s ease-out infinite alternate !important; opacity: 1 !important; transform: translateY(0) !important; }
    @keyframes pulseTarget { 0% { background-color: #fffbeb; } 100% { background-color: #fde68a; } }

    /* TEXT & BADGES RAMPING */
    .doc-title { font-weight: 700; color: #0f172a; font-size: 13px; margin-bottom: 2px; line-height: 1.3; }
    .doc-desc { color: #64748b; font-size: 11px; line-height: 1.4; max-width: 100%; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .ea-badge-aktif, .ea-badge-inaktif, .ea-badge-permanen, .badge-lokasi-umum, .badge-lokasi-internal { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; }
    .ea-badge-aktif { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .ea-badge-inaktif { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .ea-badge-permanen { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
    .badge-lokasi-umum { background: #eff6ff; color: #0284c7; border: 1px solid #bae6fd; }
    .badge-lokasi-internal { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

    .badge-kp { background: #f8fafc; color: #0f172a; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 10px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px; }
    .badge-kp i { color: #C8A35A; }
    
    .status-file-ada, .status-file-kosong { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; transition: 0.2s; }
    .status-file-ada { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .status-file-ada:hover { background: #dcfce7; }
    .status-file-kosong { background: #f8fafc; color: #64748b; border: 1px dashed #cbd5e1; }
    
    /* =========================================
       🌟 TOMBOL AKSI COMPACT & HOVER 🌟
       ========================================= */
    .action-group-table { display: flex; gap: 4px; justify-content: center; align-items: center; flex-wrap: nowrap; }
    
    .btn-t-view, .btn-t-edit, .btn-t-del, .btn-t-qr, .btn-t-kp {
        padding: 6px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        border: 1px solid transparent;
        cursor: pointer;
        margin: 0;
    }
    
    .btn-t-view { background: #f0f9ff; color: #0284c7; border-color: #e0f2fe; }
    .btn-t-view:hover { background: #0284c7; color: #ffffff; border-color: #0284c7; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2); }
    
    .btn-t-qr { background: #fdf4ff; color: #a855f7; border-color: #f3e8ff; }
    .btn-t-qr:hover { background: #9333ea; color: #ffffff; border-color: #9333ea; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(147, 51, 234, 0.2); }

    .btn-t-kp { background: #fefce8; color: #d97706; border-color: #fef08a; }
    .btn-t-kp:hover { background: #d97706; color: #ffffff; border-color: #d97706; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2); }
    
    .btn-t-edit { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .btn-t-edit:hover { background: #475569; color: #ffffff; border-color: #475569; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(71, 85, 105, 0.2); }
    
    .btn-t-del { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
    .btn-t-del:hover { background: #ef4444; color: #ffffff; border-color: #ef4444; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }

    /* Modal Styling */
    .detail-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #cbd5e1; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { font-size: 12px; color: #64748b; font-weight: 600; }
    .detail-value { font-size: 13px; color: #1e293b; text-align: right; }
    .desc-scroll { max-height: 100px; overflow-y: auto; padding-right: 5px; margin-top: 5px; }
    .desc-scroll::-webkit-scrollbar { width: 4px; }
    .desc-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .file-box-ada { background: #f0fdf4; border-color: #bbf7d0; }
    .file-box-ada .file-box-title { color: #16a34a; }
    .file-box-kosong { background: #f8fafc; border-color: #e2e8f0; }
    .file-box-kosong .file-box-title { color: #64748b; }

    /* =======================================================
       🌟 PAGINATION STYLING (TERPISAH & COKLAT KEEMASAN) 🌟
       ======================================================= */
    .pagination-wrapper { margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .page-count-text { color: #0f172a; font-weight: 700; }
    
    .pagination-links nav > div.d-sm-none { display: none !important; } 
    .pagination-links nav > div.d-none.d-sm-flex { display: flex !important; justify-content: flex-end !important; width: 100%; }
    .pagination-links nav > div.d-sm-flex > div:first-child { display: none !important; } /* Sembunyikan "Showing to..." bawaan */
    .pagination-links nav > div.d-sm-flex > div:last-child { margin-left: auto; display: flex; justify-content: flex-end; width: 100%; }
    .pagination-links nav { margin: 0; width: 100%; }
    
    .pagination { margin-bottom: 0; display: flex; gap: 6px; flex-wrap: wrap; box-shadow: none; }
    .page-item { margin: 0; }
    .page-item .page-link {
        border-radius: 8px !important;
        margin-left: 0 !important;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        transition: all 0.2s ease;
    }
    .page-item .page-link:hover {
        background-color: #f8fafc;
        color: #C8A35A;
        border-color: #cbd5e1;
    }
    .page-item.active .page-link {
        background-color: #C8A35A !important;
        border-color: #C8A35A !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(200, 163, 90, 0.3);
    }
    .page-item.disabled .page-link {
        background-color: #f8fafc !important;
        color: #cbd5e1 !important;
        border-color: #e2e8f0 !important;
    }
    
    @media (max-width: 768px) {
        .header-banner { padding: 25px 20px; text-align: center; }
        .folder-info-wrapper { flex-direction: column; justify-content: center; gap: 10px; margin-bottom: 15px; }
        .folder-desc { margin-left: 0; text-align: center; font-size: 13px; }
        .folder-icon-box { margin: 0 auto; width: 50px; height: 50px; font-size: 24px; }
        .folder-title { font-size: 22px; }
        .smart-action-bar { padding: 15px; position: relative; top: 0; } 
        .action-buttons { flex-direction: column; width: 100%; }
        .action-buttons .btn-action { width: 100%; justify-content: center; }
        .search-area { width: 100%; margin: 0; }
        .bottom-bar-actions { flex-direction: column; align-items: stretch; gap: 10px; }
        .entries-capsule { width: 100%; justify-content: space-between; } 
        .pagination-wrapper { flex-direction: column; align-items: center; text-align: center; gap: 15px; }
        .pagination-links nav > div.d-sm-flex > div:last-child { justify-content: center !important; margin-left: 0; }
    }

    /* =========================================
       🌙 MODE GELAP (DARK MODE) SEPENUHNYA 🌙
       ========================================= */
    body.dark-mode .smart-action-bar { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .table-card { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .ea-table th { background: #1e293b !important; color: #cbd5e1 !important; border-bottom-color: #334155 !important; }
    body.dark-mode .ea-table td { border-top-color: #1e293b !important; border-bottom-color: #1e293b !important; }
    body.dark-mode .ea-table tbody tr { background-color: #0f172a !important; }
    body.dark-mode .ea-table tbody tr:hover { background-color: #1e293b !important; }
    
    body.dark-mode .ea-table tbody tr:target { background-color: rgba(217, 119, 6, 0.25) !important; border-left-color: #fbbf24 !important; opacity: 1 !important; transform: translateY(0) !important; }
    body.dark-mode .row-target-blink { animation: pulseTargetDark 2s ease-out infinite alternate !important; opacity: 1 !important; transform: translateY(0) !important; }
    @keyframes pulseTargetDark { 0% { background-color: rgba(217, 119, 6, 0.25); } 100% { background-color: rgba(251, 191, 36, 0.15); } }

    body.dark-mode .entries-capsule { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .entries-capsule span { color: #94a3b8 !important; }
    body.dark-mode .entries-select { color: #f8fafc !important; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23cbd5e1' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important; }
    body.dark-mode .entries-select option { background: #0f172a !important; color: #f8fafc !important; }
    
    body.dark-mode .btn-mode-hapus { background: #1e293b !important; color: #fb7185 !important; border-color: #334155 !important; }
    body.dark-mode .btn-mode-hapus:hover { background: #ef4444 !important; color: white !important; border-color: #ef4444 !important; }
    body.dark-mode .btn-mode-hapus.active { background: #C8A35A !important; color: white !important; border-color: #C8A35A !important; }
    body.dark-mode .search-input { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    body.dark-mode .search-input:focus { background: #0b1120 !important; border-color: #C8A35A !important; }
    
    body.dark-mode .ea-badge-aktif { background: #064e3b !important; color: #34d399 !important; border-color: #065f46 !important; }
    body.dark-mode .ea-badge-inaktif { background: #7f1d1d !important; color: #fca5a5 !important; border-color: #991b1b !important; }
    body.dark-mode .ea-badge-permanen { background: #1e3a8a !important; color: #93c5fd !important; border-color: #1e40af !important; }
    body.dark-mode .badge-lokasi-umum { background: #0c4a6e !important; color: #7dd3fc !important; border-color: #075985 !important; }
    body.dark-mode .badge-lokasi-internal { background: #334155 !important; color: #cbd5e1 !important; border-color: #475569 !important; }
    body.dark-mode .badge-kp { background: #1e293b !important; color: #fde68a !important; border-color: #334155 !important; }
    body.dark-mode .status-file-ada { background: #064e3b !important; color: #34d399 !important; border-color: #065f46 !important; }
    body.dark-mode .status-file-ada:hover { background: #065f46 !important; }
    body.dark-mode .status-file-kosong { background: #1e293b !important; color: #94a3b8 !important; border-color: #334155 !important; }
    
    /* Tombol Aksi Tabel Mode Gelap */
    body.dark-mode .btn-t-view { background: #1e293b !important; color: #38bdf8 !important; border-color: #334155 !important; }
    body.dark-mode .btn-t-view:hover { background: #0284c7 !important; color: #ffffff !important; border-color: #0284c7 !important; }
    body.dark-mode .btn-t-qr { background: #1e293b !important; color: #c084fc !important; border-color: #334155 !important; }
    body.dark-mode .btn-t-qr:hover { background: #9333ea !important; color: #ffffff !important; border-color: #9333ea !important; }
    body.dark-mode .btn-t-kp { background: #1e293b !important; color: #fde047 !important; border-color: #334155 !important; }
    body.dark-mode .btn-t-kp:hover { background: #ca8a04 !important; color: #ffffff !important; border-color: #ca8a04 !important; }
    body.dark-mode .btn-t-edit { background: #1e293b !important; color: #94a3b8 !important; border-color: #334155 !important; }
    body.dark-mode .btn-t-edit:hover { background: #475569 !important; color: #ffffff !important; border-color: #475569 !important; }
    body.dark-mode .btn-t-del { background: #1e293b !important; color: #fb7185 !important; border-color: #334155 !important; }
    body.dark-mode .btn-t-del:hover { background: #e11d48 !important; color: #ffffff !important; border-color: #e11d48 !important; }

    /* 🌟 PERBAIKAN: Tombol Atas (Cetak/Tambah) Mode Gelap (Transparan Elegan) 🌟 */
    body.dark-mode .btn-pdf { background: rgba(225, 29, 72, 0.15) !important; color: #fb7185 !important; border-color: rgba(225, 29, 72, 0.3) !important; }
    body.dark-mode .btn-pdf:hover { background: #e11d48 !important; color: white !important; border-color: #e11d48 !important; }
    
    body.dark-mode .btn-add { background: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; border-color: rgba(16, 185, 129, 0.3) !important; }
    body.dark-mode .btn-add:hover { background: #059669 !important; color: white !important; border-color: #059669 !important; }
    
    body.dark-mode .btn-back { background: #1e293b !important; color: #cbd5e1 !important; border-color: #334155 !important; }
    body.dark-mode .btn-back:hover { background: #334155 !important; color: white !important; border-color: #475569 !important; }

    /* 🌟 DARK MODE UNTUK PAGINATION TERPISAH 🌟 */
    body.dark-mode .pagination-wrapper { border-top-color: #1e293b !important; }
    
    body.dark-mode .page-item .page-link { 
        background-color: #1e293b !important; 
        border-color: #334155 !important; 
        color: #cbd5e1 !important; 
    }
    body.dark-mode .page-item .page-link:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
    }
    
    body.dark-mode .page-item.active .page-link { 
        background-color: #C8A35A !important; 
        border-color: #C8A35A !important; 
        color: #ffffff !important; 
        box-shadow: 0 4px 10px rgba(200, 163, 90, 0.2) !important;
    }
    body.dark-mode .page-item.disabled .page-link { 
        background-color: #0f172a !important; 
        color: #475569 !important; 
        border-color: #1e293b !important; 
    }

    /* Memastikan Teks Hitungan Halaman Berwarna Terang di Dark Mode */
    body.dark-mode .ea-table td.col-no,
    body.dark-mode .ea-table .text-muted,
    body.dark-mode .page-count-text {
        color: #f8fafc !important;
    }

    /* Modal Dark Mode Styling */
    body.dark-mode .modal-content { background: #0f172a !important; border-color: #1e293b !important; }
    body.dark-mode .modal-content .p-4[style*="background: #f8fafc"] { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .detail-item { border-bottom-color: #334155 !important; }
    body.dark-mode .detail-label { color: #94a3b8 !important; }
    body.dark-mode .detail-value { color: #f8fafc !important; }
    body.dark-mode .text-dark { color: #ffffff !important; }
    body.dark-mode .text-muted { color: #94a3b8 !important; }
    body.dark-mode .desc-scroll::-webkit-scrollbar-thumb { background: #475569 !important; }
    body.dark-mode .file-box-ada { background: #064e3b !important; border-color: #065f46 !important; }
    body.dark-mode .file-box-ada .file-box-title { color: #34d399 !important; }
    body.dark-mode .file-box-ada .detail-value { color: #f8fafc !important; }
    body.dark-mode .file-box-kosong { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .file-box-kosong .file-box-title { color: #94a3b8 !important; }
    body.dark-mode .file-box-kosong .detail-value { color: #94a3b8 !important; }
    body.dark-mode #modalQR .bg-white { background: #ffffff !important; border-color: #ffffff !important; } 
</style>
@endpush

@section('content')
<div class="custom-page container-fluid">
    @include('partials.alerts')

    <div class="header-banner">
        <div class="folder-info-wrapper">
            <div class="folder-icon-box">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <h1 class="folder-title">Data Arsip</h1>
            <span class="kode-badge">{{ $kode }}</span>
        </div>
        <p class="folder-desc">Inventaris lengkap dokumen fisik dan rekam digital yang berada di bawah naungan klasifikasi ini.</p>
    </div>

    <form action="{{ route('arsip.folder.isi', $kode) }}" method="GET" id="smartActionBar" class="smart-action-bar">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3">
            <div class="action-buttons">
                @auth
                    <a href="{{ route('arsip.cetak_pdf', $kode) }}" target="_blank" class="btn-action btn-pdf">
                        <i class="fa-solid fa-file-pdf"></i> Cetak Laporan
                    </a>
                    
                    @can('admin')
                    <a href="{{ route('arsip.create', $kode) }}" class="btn-action btn-add">
                        <i class="fa-solid fa-plus"></i> Arsip Baru
                    </a>
                    @endcan
                    
                    <a href="{{ route('arsip.folders') }}" class="btn-action btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-action btn-add" style="background: #fffbeb; color: #d97706; border-color: #fde68a;">
                        <i class="fa-solid fa-right-to-bracket"></i> Login untuk Kelola
                    </a>
                @endauth
            </div>

            <div class="search-area ms-md-auto">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input shadow-sm" placeholder="Telusuri dokumen spesifik..." value="{{ request('search') }}">
                <button type="submit" class="btn-search">Cari</button>
            </div>
        </div>

        <div class="bottom-bar-actions mt-1">
            <div class="d-flex gap-2 align-items-center flex-wrap w-100">
                <div class="entries-capsule shadow-sm">
                    <span class="text-muted" style="font-size: 12px; font-weight: 600;">Tampilkan:</span>
                    <select name="per_page" class="form-select form-select-sm entries-select" style="min-width: 80px;" onchange="this.form.submit();">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>Semua Data</option>
                    </select>
                </div>
                <div class="entries-capsule shadow-sm">
                    <span class="text-muted" style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-filter"></i> Status JRA:</span>
                    <select name="filter_retensi" class="form-select form-select-sm entries-select filter-select" onchange="this.form.submit();">
                        <option value="semua" {{ request('filter_retensi') == 'semua' ? 'selected' : '' }}>Semua Arsip</option>
                        <option value="aktif" {{ request('filter_retensi') == 'aktif' ? 'selected' : '' }}>Aktif (Masih Berlaku)</option>
                        <option value="inaktif" {{ request('filter_retensi') == 'inaktif' ? 'selected' : '' }}>Inaktif (Jatuh Tempo)</option>
                    </select>
                </div>
                <div class="entries-capsule shadow-sm">
                    <span class="text-muted" style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-paperclip"></i> Status File:</span>
                    <select name="filter_file" class="form-select form-select-sm entries-select filter-select" onchange="this.form.submit();">
                        <option value="semua" {{ request('filter_file') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="ada" {{ request('filter_file') == 'ada' ? 'selected' : '' }}>Tersedia (Ada File)</option>
                        <option value="kosong" {{ request('filter_file') == 'kosong' ? 'selected' : '' }}>Kosong (Tanpa File)</option>
                    </select>
                </div>
                <div class="entries-capsule shadow-sm">
                    <span class="text-muted" style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-location-dot"></i> Lokasi Fisik:</span>
                    <select name="filter_lokasi" class="form-select form-select-sm entries-select filter-select" onchange="this.form.submit();">
                        <option value="semua" {{ request('filter_lokasi') == 'semua' ? 'selected' : '' }}>Semua Lokasi</option>
                        <option value="Internal" {{ request('filter_lokasi') == 'Internal' ? 'selected' : '' }}>Internal (Ruangan)</option>
                        <option value="Bagian Umum" {{ request('filter_lokasi') == 'Bagian Umum' ? 'selected' : '' }}>Diserahkan ke Bag. Umum</option>
                    </select>
                </div>
            </div>

            @auth
            @can('admin')
            <div class="d-flex gap-2 align-items-center flex-wrap ms-auto mt-2 mt-lg-0">
                <button type="button" id="btnToggleDeleteMode" class="btn-mode-hapus shadow-sm">
                    <i class="fa-solid fa-list-check"></i> <span>Mode Hapus Massal</span>
                </button>
                <button type="button" id="btnBulkDelete" class="btn-bulk-delete" onclick="submitBulkDelete()">
                    <i class="fa-solid fa-trash-can"></i> Eksekusi Hapus (<span id="selectedCount">0</span>)
                </button>
            </div>
            @endcan
            @endauth
        </div>
    </form>

    <form id="bulkDeleteForm" action="{{ route('arsip.bulk_delete') }}" method="POST">
        @csrf @method('DELETE')
        
        <div class="table-card">
            <div class="table-responsive">
                <table class="ea-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox d-none" style="width: 3%;">
                                @auth
                                @can('admin')
                                <input type="checkbox" id="checkAll" class="cb-custom" title="Pilih Semua">
                                @endcan
                                @endauth
                            </th>
                            <th class="col-no" style="width: 5%;">NO</th>
                            <th class="col-info" style="width: 35%;">INFORMASI DOKUMEN</th>
                            <th style="width: 15%;">TAHUN & RETENSI</th>
                            <th style="width: 10%;">KODE KP</th>
                            <th style="width: 12%;">STATUS FILE</th>
                            <th class="col-aksi" style="width: 20%; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arsips as $index => $arsip)
                        
                        @php
                            $currentYear = (int)date('Y');
                            preg_match('/\d{4}/', $arsip->tahun_berkas, $matches);
                            $tahunSistem = !empty($matches) ? (int)$matches[0] : (int)date('Y');
                            $retensiAktif = (int)($arsip->retensi_aktif ?? 0);
                            $batasAktif = $tahunSistem + $retensiAktif;
                            $isInaktif = $currentYear > $batasAktif;
                            $nasibAkhir = strtolower($arsip->nasib_akhir ?? 'musnah');
                        @endphp

                        <tr id="arsip-{{ $arsip->id }}" class="row-item" style="animation-delay: {{ $index * 0.08 }}s;">
                            <td class="col-checkbox d-none">
                                @auth
                                @can('admin')
                                <input type="checkbox" name="ids[]" value="{{ $arsip->id }}" class="cb-custom check-item">
                                @endcan
                                @endauth
                            </td>
                            <td class="col-no text-muted fw-bold" style="font-size: 14px;">
                                {{ ($arsips->currentPage() - 1) * $arsips->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <div style="min-width: 0;">
                                    <div class="doc-title">{{ $arsip->nama_berkas }}</div>
                                    <div class="doc-desc">{{ $arsip->deskripsi_berkas }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 13px; margin-bottom: 2px;">{{ $arsip->tahun_berkas }}</div>
                                @if(!$isInaktif)
                                    <span class="ea-badge-aktif" title="Masih dalam masa aktif s.d tahun {{ $batasAktif }}"><i class="fa-solid fa-shield-check"></i> Aktif (s.d {{ $batasAktif }})</span>
                                @else
                                    @if($nasibAkhir == 'permanen')
                                        <span class="ea-badge-permanen"><i class="fa-solid fa-building-columns"></i> Inaktif (Permanen)</span>
                                    @else
                                        <span class="ea-badge-inaktif"><i class="fa-solid fa-fire"></i> Inaktif (Musnah)</span>
                                    @endif
                                @endif
                                <div class="mt-1">
                                    @if(isset($arsip->status_lokasi) && $arsip->status_lokasi == 'Bagian Umum')
                                        <span class="badge-lokasi-umum"><i class="fa-solid fa-check-double"></i> Ke Bag. Umum</span>
                                    @else
                                        <span class="badge-lokasi-internal"><i class="fa-solid fa-box-archive"></i> Internal</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge-kp"><i class="fa-solid fa-tag"></i> {{ $arsip->kode_arsip }}</span>
                            </td>
                            <td>
                                @if($arsip->file_dokumen)
                                    <a href="{{ asset('storage/' . $arsip->file_dokumen) }}" target="_blank" class="status-file-ada" title="Lihat/Buka Dokumen">
                                        <i class="fa-solid fa-file-circle-check fs-6"></i> Tersedia
                                    </a>
                                @else
                                    <span class="status-file-kosong">
                                        <i class="fa-solid fa-file-circle-xmark"></i> Kosong
                                    </span>
                                @endif
                            </td>
                            <td class="col-aksi">
                                <div class="action-group-table">
                                    <button type="button" class="btn-t-view" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $arsip->id }}" title="Lihat Rincian Detail">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </button>

                                    @auth
                                    <button type="button" class="btn-t-qr" data-bs-toggle="modal" data-bs-target="#modalQR{{ $arsip->id }}" title="Scan QR Code untuk akses">
                                        <i class="fa-solid fa-qrcode"></i> Scan QR
                                    </button>

                                    @can('admin')
                                    <button type="button" class="btn-t-kp" data-bs-toggle="modal" data-bs-target="#modalEditKP{{ $arsip->id }}" title="Ubah Kode Klasifikasi (KP) Cepat">
                                        <i class="fa-solid fa-tags"></i> Edit KP
                                    </button>
                                    
                                    <a href="{{ route('arsip.edit', $arsip->id) }}" class="btn-t-edit" title="Edit Full Data">
                                        <i class="fa-solid fa-pen-clip"></i>
                                    </a>
                                    <button type="button" class="btn-t-del" title="Hapus Data" onclick="confirmSingleDelete({{ $arsip->id }})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    @endcan
                                    @endauth
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL DETAIL DOKUMEN & VERIFIKASI DOWNLOAD --}}
                        <div class="modal fade" id="modalDetail{{ $arsip->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                    <div class="modal-header border-0 p-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-bottom: 3px solid #C8A35A !important;">
                                        <h5 class="modal-title fw-bold" style="letter-spacing: 0.5px; font-size: 15px;">
                                            <i class="fa-solid fa-magnifying-glass-chart me-2" style="color: #C8A35A;"></i> Rincian Arsip
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="row g-0">
                                            <div class="col-md-5 p-4" style="background: #f8fafc; border-right: 1px solid #e2e8f0;">
                                                <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-tags text-muted me-1"></i> Identitas Dokumen</h6>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="detail-item">
                                                        <span class="detail-label">Kode KP</span>
                                                        <span class="detail-value fw-bold" style="color: #C8A35A;">{{ $arsip->kode_arsip }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Tahun</span>
                                                        <span class="detail-value fw-bold">{{ $arsip->tahun_berkas }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Status JRA</span>
                                                        <span class="detail-value">
                                                            @if(!$isInaktif)
                                                                <span class="badge bg-success rounded-pill px-2">Aktif</span>
                                                            @else
                                                                <span class="badge bg-danger rounded-pill px-2">Musnah</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Lokasi Fisik</span>
                                                        <span class="detail-value">{{ $arsip->status_lokasi ?? 'Internal' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Warna Berkas</span>
                                                        <span class="detail-value">{{ $arsip->warna_berkas ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-7 p-4 d-flex flex-column">
                                                <div class="mb-3">
                                                    <span style="font-size: 11px; font-weight: 700; color: #C8A35A; text-transform: uppercase; letter-spacing: 1px;">Judul Berkas</span>
                                                    <h5 class="fw-bold text-dark mt-1 mb-0" style="line-height: 1.4; font-size: 16px;">{{ $arsip->nama_berkas }}</h5>
                                                </div>
                                                <div class="mb-auto">
                                                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Deskripsi / Uraian</span>
                                                    <div class="mt-1 text-muted desc-scroll" style="font-size: 12px; line-height: 1.6;">
                                                        {!! $arsip->deskripsi_berkas ? nl2br(e($arsip->deskripsi_berkas)) : '<span class="fst-italic">Tidak ada uraian catatan tambahan.</span>' !!}
                                                    </div>
                                                </div>

                                                <div class="mt-3 p-3 rounded-3 border {{ $arsip->file_dokumen ? 'file-box-ada' : 'file-box-kosong' }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="file-box-title" style="font-size: 11px; font-weight: 700; margin-bottom: 2px;">
                                                                <i class="fa-solid fa-paperclip"></i> File Digital
                                                            </div>
                                                            <div style="font-size: 12px; font-weight: 600;" class="detail-value">
                                                                {{ $arsip->file_dokumen ? 'Tersedia di Server' : 'Kosong' }}
                                                            </div>
                                                        </div>
                                                        
                                                        @if($arsip->file_dokumen)
                                                            @auth
                                                                @if(auth()->user()->role == 'admin')
                                                                    <a href="{{ asset('storage/' . $arsip->file_dokumen) }}" download class="btn btn-sm btn-success fw-bold px-3 rounded-pill shadow-sm" style="font-size: 11px;">
                                                                        <i class="fa-solid fa-download"></i> Unduh File
                                                                    </a>
                                                                @else
                                                                    @php
                                                                        $izin = \App\Models\IzinAkses::where('user_id', auth()->id())
                                                                                    ->where('arsip_id', $arsip->id)
                                                                                    ->orderBy('created_at', 'desc')
                                                                                    ->first();
                                                                    @endphp

                                                                    @if($izin && $izin->status == 'disetujui' && $izin->hak_unduh)
                                                                        <a href="{{ asset('storage/' . $arsip->file_dokumen) }}" download class="btn btn-sm btn-success fw-bold px-3 rounded-pill shadow-sm" style="font-size: 11px;">
                                                                            <i class="fa-solid fa-download"></i> Unduh File
                                                                        </a>
                                                                    @elseif($izin && $izin->status == 'disetujui' && !$izin->hak_unduh)
                                                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-eye"></i> Izin: Hanya Lihat</span>
                                                                    @elseif($izin && $izin->status == 'menunggu')
                                                                        <span class="badge bg-secondary"><i class="fa-solid fa-hourglass-half"></i> Menunggu Izin Admin</span>
                                                                    @else
                                                                        <form action="{{ route('qr.minta_izin', $arsip->id) }}" method="POST" class="m-0 p-0">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-sm btn-primary fw-bold px-3 rounded-pill shadow-sm" style="font-size: 11px;">
                                                                                <i class="fa-solid fa-lock"></i> Minta Izin Unduh
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                @endif
                                                            @else
                                                                <a href="{{ route('login') }}" class="btn btn-sm btn-warning fw-bold px-3 rounded-pill shadow-sm text-dark" style="font-size: 11px;">
                                                                    <i class="fa-solid fa-right-to-bracket"></i> Login untuk Akses
                                                                </a>
                                                            @endauth
                                                        @endif
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- MODAL QR CODE KHUSUS DOKUMEN INI --}}
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
                                        <p class="text-muted small mb-0" style="line-height: 1.4;">Arahkan kamera HP ke QR Code ini untuk meminta izin akses ke dokumen terkait.</p>
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
                                        <h6 class="modal-title fw-bold" style="font-size: 14px;"><i class="fa-solid fa-tags" style="color: #fde047;"></i> Ubah Klasifikasi</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('arsip.update_kp', $arsip->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Kode Saat Ini</label>
                                                <div class="fw-bold text-dark p-2 border rounded text-center" style="background: rgba(0,0,0,0.02);">{{ $arsip->kode_arsip }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Kode Baru <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_arsip" class="form-control form-control-sm text-center fw-bold" value="{{ $arsip->kode_arsip }}" required placeholder="Contoh: KP.15.01">
                                                <small class="text-muted mt-2 d-block text-center" style="font-size: 10.5px; line-height: 1.4;">Mengubah kode KP akan memindahkan dokumen ini ke Folder Klasifikasi tersebut.</small>
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

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5" style="border: none;">
                                <div style="color: #cbd5e1; font-size: 60px; margin-bottom: 20px;"><i class="fa-solid fa-box-open"></i></div>
                                <h5 class="fw-bold text-dark mb-2">Folder Ini Kosong</h5>
                                <p class="text-muted">Belum ada dokumen yang diarsipkan ke dalam sistem.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                <div class="text-muted small fw-medium" style="color: #94a3b8 !important;">
                    Menampilkan <b class="page-count-text">{{ $arsips->firstItem() ?? 0 }}</b> hingga <b class="page-count-text">{{ $arsips->lastItem() ?? 0 }}</b> dari total <b class="page-count-text">{{ $arsips->total() }}</b> entri arsip.
                </div>
                <div class="pagination-links">
                    {{ $arsips->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </form>
    
    @auth
    @can('admin')
    <form id="singleDeleteForm" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    @endcan
    @endauth
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(window.location.hash) {
            const targetId = window.location.hash.substring(1);
            const targetElement = document.getElementById(targetId);
            if(targetElement) {
                setTimeout(() => {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetElement.classList.add('row-target-blink');
                }, 400); 
            }
        }

        const actionBar = document.getElementById('smartActionBar');
        let isScrolled = false;
        let hideTimeout; 
        window.addEventListener('scroll', () => {
            if (window.scrollY > 250) {
                isScrolled = true;
                if (!actionBar.matches(':hover')) { actionBar.classList.add('hide-up'); actionBar.classList.remove('peek-down'); }
            } else {
                isScrolled = false;
                actionBar.classList.remove('hide-up'); actionBar.classList.remove('peek-down');
            }
        });
        document.addEventListener('mousemove', (e) => {
            if (!isScrolled) return;
            if (e.clientY < 150 || actionBar.matches(':hover')) {
                clearTimeout(hideTimeout); 
                actionBar.classList.remove('hide-up'); actionBar.classList.add('peek-down');
            } else {
                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(() => {
                    if (!actionBar.matches(':hover')) { actionBar.classList.add('hide-up'); actionBar.classList.remove('peek-down'); }
                }, 500); 
            }
        });
        const btnToggleDeleteMode = document.getElementById('btnToggleDeleteMode');
        const checkboxCols = document.querySelectorAll('.col-checkbox');
        const checkAll = document.getElementById('checkAll');
        const checkItems = document.querySelectorAll('.check-item');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const selectedCountText = document.getElementById('selectedCount');
        let deleteModeActive = false;
        if (btnToggleDeleteMode) {
            btnToggleDeleteMode.addEventListener('click', function() {
                deleteModeActive = !deleteModeActive;
                this.classList.toggle('active');
                if (deleteModeActive) {
                    this.innerHTML = '<i class="fa-solid fa-xmark"></i> <span>Batal Hapus</span>';
                    checkboxCols.forEach(col => col.classList.remove('d-none'));
                } else {
                    this.innerHTML = '<i class="fa-solid fa-list-check"></i> <span>Mode Hapus Massal</span>';
                    checkboxCols.forEach(col => col.classList.add('d-none'));
                    if(checkAll) checkAll.checked = false;
                    checkItems.forEach(item => {
                        item.checked = false;
                        item.closest('tr').classList.remove('row-selected');
                    });
                    updateBulkDeleteButton();
                }
            });
        }
        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.check-item:checked').length;
            selectedCountText.innerText = checkedCount;
            checkItems.forEach(item => {
                const tr = item.closest('tr');
                if(item.checked) {
                    tr.classList.add('row-selected');
                } else {
                    tr.classList.remove('row-selected');
                }
            });
            if (checkedCount > 0) {
                btnBulkDelete.classList.add('show-anim');
            } else {
                btnBulkDelete.classList.remove('show-anim');
                if(checkAll) checkAll.checked = false; 
            }
        }
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(item => {
                    item.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }
        checkItems.forEach(item => {
            item.addEventListener('change', function() {
                updateBulkDeleteButton();
                if(checkAll) {
                    const allChecked = document.querySelectorAll('.check-item:checked').length === checkItems.length;
                    checkAll.checked = allChecked;
                }
            });
        });
    });

    function submitBulkDelete() {
        const count = document.getElementById('selectedCount').innerText;
        Swal.fire({
            title: 'Buang ' + count + ' Dokumen?',
            text: "Arsip yang dipilih akan dipindahkan ke Tong Sampah!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#475569', 
            confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Buang!',
            cancelButtonText: 'Batal',
            reverseButtons: true, 
            backdrop: `rgba(15, 23, 42, 0.4)`, 
            customClass: { popup: 'border border-light shadow-lg', title: 'fs-4 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }

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