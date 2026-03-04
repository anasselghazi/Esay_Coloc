<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pseudo',
        'photo',
        'reputation',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function ownedColocations()
    {
        return $this->hasMany(Colocation::class,'owner_id');

    }
    public function colocations()
    {
        
        return $this->belongsToMany(Colocation::class)
                    ->withPivot('role', 'joined_at', 'left_at');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function invitationsSent()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function paymentsFrom()
    {
        return $this->hasMany(Payment::class, 'from_user_id');
    }

    public function paymentsTo()
    {
        return $this->hasMany(Payment::class, 'to_user_id');
    }

    public function isBanned()
    {
        return $this->status === 'banned';
    }
}
