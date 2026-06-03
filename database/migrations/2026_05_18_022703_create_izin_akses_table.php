<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('izin_akses', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Siapa pegawai yang meminta izin)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke tabel arsips (Dokumen mana yang ingin dilihat/diunduh)
            $table->foreignId('arsip_id')->constrained('arsips')->onDelete('cascade');
            
            // Status persetujuan dari Admin SDM
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            
            // Hak eksklusif untuk mendownload file (Default: false / hanya boleh lihat)
            $table->boolean('hak_unduh')->default(false);
            
            $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_akses');
    }
};