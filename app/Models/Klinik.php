<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Klinik extends Model
{
    use HasFactory;

    protected $table = 'kliniks';

    public const SERVICE_NAMES = [
        // Perawatan Wajah Dasar
        'facial_basic' => 'Facial Basic',
        'facial_acne' => 'Facial Acne',
        'facial_brightening' => 'Facial Brightening',
        'blackhead_removal' => 'Blackhead Removal',
        'hydrafacial' => 'HydraFacial',

        // Peeling Treatment
        'chemical_peel' => 'Chemical Peeling',
        'carbon_peel' => 'Carbon Peel',
        'milk_peel' => 'Milk Peel',

        // Laser Treatment
        'laser_rejuvenation' => 'Laser Rejuvenation',
        'laser_acne' => 'Laser Acne',
        'ipl_photorejuvenation' => 'IPL Photorejuvenation',
        'laser_hair_removal' => 'Laser Hair Removal',
        'co2_laser' => 'CO2 Laser',

        // Injeksi & Estetik
        'botox' => 'Botox',
        'filler' => 'Dermal Filler',
        'skinbooster' => 'Skin Booster',
        'vitamin_injection' => 'Vitamin Injection',
        'whitening_injection' => 'Whitening Injection',

        // Advanced Treatment
        'microneedling' => 'Microneedling',
        'rf_microneedling' => 'RF Microneedling',
        'hifu' => 'HIFU',

        // Specialized Treatment
        'prp_therapy' => 'PRP Therapy',
        'thread_lift' => 'Thread Lift',
        'cryotherapy' => 'Cryotherapy',
        'sclerotherapy' => 'Sclerotherapy',

        // Body Treatment
        'body_contouring' => 'Body Contouring',
        'cavitation' => 'Cavitation',
        'radiofrequency' => 'Radiofrequency',
        'coolsculpting' => 'CoolSculpting'
    ];

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
        'status',
        'min_price',
        'max_price',
        'services',
        'service_prices',
        'deskripsi'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'services' => 'array',
        'service_prices' => 'array',
    ];

    // Accessor untuk mendapatkan harga yang diformat
    public function getFormattedPriceAttribute($serviceName)
    {
        if (isset($this->service_prices[$serviceName])) {
            return 'Rp ' . number_format($this->service_prices[$serviceName], 0, ',', '.');
        }
        return 'Harga tidak tersedia';
    }

    // Accessor untuk mendapatkan display price range
    public function getPriceRangeDisplayAttribute()
    {
        if ($this->min_price && $this->max_price) {
            return 'Rp ' . number_format($this->min_price, 0, ',', '.') . ' - Rp ' . number_format($this->max_price, 0, ',', '.');
        } elseif ($this->min_price) {
            return 'Mulai dari Rp ' . number_format($this->min_price, 0, ',', '.');
        }
        return 'Harga belum tersedia';
    }

    // Accessor untuk mendapatkan services yang sudah diformat (menghilangkan underscore)
    public function getFormattedServicesAttribute()
    {
        if (!$this->services) return [];

        return collect($this->services)->map(function($service) {
            return self::SERVICE_NAMES[$service] ?? ucwords(str_replace('_', ' ', $service));
        })->toArray();
    }
}
