<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoLokasi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'info_lokasi';

    // Kolom-kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'nama_tempat',
        'deskripsi_tempat',
        'latitude',
        'longitude',
    ];

    // Kolom yang ingin disembunyikan saat model diubah menjadi JSON
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
