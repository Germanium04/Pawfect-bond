<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalInfo extends Model
{
    protected $table = 'pet_medical_records';

    protected $fillable = [
        'pet_id',
        'vaccinated',
        'vaccinated_date',
        'vaccinated_certificate',
        'dewormed',
        'dewormed_date',
        'dewormed_certificate',
        'neutered',
        'neutered_date',
        'neutered_certificate',
    ];

    protected $casts = [
        'vaccinated' => 'boolean',
        'dewormed'   => 'boolean',
        'neutered'   => 'boolean',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }
}