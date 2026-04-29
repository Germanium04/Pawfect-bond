<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'gender',
        'birthdate',
        'age',
        'marital_status',
        'email',
        'password',
        'contact_number',
        'address',
        'role',
        'status',
        'profile_image'
    ];

    protected $hidden = [
        'password',
    ];

    // A user can post many pets
    // User.php
    public function pets()
    {
        // Change 'id' to 'user_id' to match your protected $primaryKey
        return $this->hasMany(Pet::class, 'owner_id');
    }

    // A user can send many messages
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // A user can receive many messages
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // A user can make adoption requests
    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class, 'adopter_id');
    }

    public function rehomedPets() {
        return $this->hasMany(Pet::class, 'owner_id', 'user_id')->where('status', 'rehomed');
    }

    // Pets the user has successfully adopted (records in adoptions table)
    public function adoptions()
    {
        return $this->hasMany(Adoption::class, 'adopter_id', 'user_id');
    }
}