<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    protected $primaryKey = 'pet_id';

    protected $fillable = [
        'owner_id',
        'name',
        'breed',
        'gender',
        'birthday',
        'likes',
        'dislikes',
        'personality',
        'status',
        'pet_image'
    ];

    // Pet belongs to a user (owner)
    public function owner()
    {
        // 1. owner_id is the foreign key in 'pets' table
        // 2. user_id is the primary key in 'users' table
        return $this->belongsTo(User::class, 'owner_id', 'user_id'); 
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalInfo::class, 'pet_id', 'pet_id');
    }

    // Pet can have many adoption requests
    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class, 'pet_id');
    }

    public function getAgeAttribute()
    {
        $birth = \Carbon\Carbon::parse($this->birthday);
        $age = $birth->diff(now());

        $years = $age->y;
        $months = $age->m;

        if ($years == 0) {
            return $months . ' ' . ($months == 1 ? 'month' : 'months');
        }

        return $years . ' ' . ($years == 1 ? 'year' : 'years') . 
            ', ' . $months . ' ' . ($months == 1 ? 'month' : 'months');
    }
}