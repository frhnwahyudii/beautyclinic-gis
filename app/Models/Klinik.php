<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Klinik extends Model
{
    use HasFactory;

    protected $table = 'kliniks';

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/klinik_photos/' . $this->foto);
        }
        return null;
    }

    protected $fillable = [
        'nama',
        'alamat',
        'foto',
        'latitude',
        'longitude',
        'jam_operasional',
        'telepon',
        'email',
        'instagram',
        'facebook',
        'twitter',
        'website',
        'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
