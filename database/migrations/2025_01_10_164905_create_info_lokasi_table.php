<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('info_lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tempat');
            $table->text('deskripsi_tempat');
            $table->float('latitude', 10, 6);
            $table->float('longitude', 10, 6);
            $table->timestamps();
        });
    }
    
};
