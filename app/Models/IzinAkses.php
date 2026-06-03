<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinAkses extends Model
{
    use HasFactory;

    protected $table = 'izin_akses';

    // 🌟 INI KUNCI UTAMANYA: Izinkan session_id & device_info disimpan! 🌟
    protected $fillable = [
        'user_id', 
        'arsip_id', 
        'status', 
        'hak_unduh', 
        'session_id', 
        'device_info'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function arsip()
    {
        return $this->belongsTo(Arsip::class);
    }
}