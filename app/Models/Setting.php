<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Menentukan kolom mana saja yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = ['key', 'value'];

    /**
     * Fungsi pembantu untuk mengambil Nama Aplikasi.
     * Jika admin belum mengatur nama di database, otomatis menggunakan 'KANTI'.
     */
    public static function getAppName()
    {
        // Mencari baris data di mana kolom 'key' adalah 'app_name'
        $setting = self::where('key', 'app_name')->first();
        
        // 🌟 PERBAIKAN: Default name sekarang adalah KANTI 🌟
        return $setting && $setting->value ? $setting->value : 'KANTI';
    }

    /**
     * Fungsi pembantu untuk mengambil Logo Aplikasi.
     */
    public static function getAppLogo()
    {
        $setting = self::where('key', 'app_logo')->first();
        
        // Karena file sekarang disimpan langsung di folder public/uploads,
        // kita cukup membungkus isinya dengan fungsi asset() tanpa tambahan teks apapun.
        return $setting && $setting->value ? asset($setting->value) : null; 
    }
}