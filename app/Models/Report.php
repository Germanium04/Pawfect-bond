<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reported_pet_id',
        'report_type',
        'reason',
        'status'
    ];

    // User who reported
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // User being reported
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    // Pet being reported (for post reports)
    public function reportedPet()
    {
        return $this->belongsTo(Pet::class, 'reported_pet_id', 'pet_id');
    }
}