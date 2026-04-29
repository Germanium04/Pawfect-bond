<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $primaryKey = 'message_id';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message_text',
        'is_read',
    ];

    // Message sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Message receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}