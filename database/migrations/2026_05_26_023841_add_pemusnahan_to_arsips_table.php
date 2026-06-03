<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('arsips', function (Blueprint $table) {
            // status_pemusnahan: 'aktif', 'usul_musnah', 'sudah_musnah'
            $table->string('status_pemusnahan')->default('aktif')->after('nasib_akhir');
            $table->string('file_bap')->nullable()->after('status_pemusnahan'); 
        });
    }
    public function down()
    {
        Schema::table('arsips', function (Blueprint $table) {
            $table->dropColumn(['status_pemusnahan', 'file_bap']);
        });
    }
};
