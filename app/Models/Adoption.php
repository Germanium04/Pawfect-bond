<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adoption extends Model
{
    protected $primaryKey = 'adoption_id';

    protected $fillable = [
        'pet_id',
        'giver_id',
        'adopter_id',
        'request_id',
        'approved_by',
        'adoption_date'
    ];

    // Relationships

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }

    public function adopter()
    {
        return $this->belongsTo(User::class, 'adopter_id', 'user_id');
    }

    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id', 'user_id');
    }

    public function request()
    {
        return $this->belongsTo(AdoptionRequest::class, 'request_id', 'request_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}