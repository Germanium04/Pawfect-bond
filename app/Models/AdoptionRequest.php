<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdoptionRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'pet_id',
        'adopter_id',
        'status'
    ];

    // Request belongs to a pet
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    // Request belongs to a user (adopter)
    public function adopter()
    {
        return $this->belongsTo(User::class, 'adopter_id');
    }

    public function adoption()
    {
        return $this->hasOne(Adoption::class, 'request_id', 'request_id');
    }
}