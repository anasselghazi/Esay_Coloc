<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['colocation_id', 'invited_by', 'email', 'token', 'status', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }
}
