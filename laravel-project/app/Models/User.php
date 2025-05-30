<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
         'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

     public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEditor()
    {
        return $this->role === 'editor';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managedConferences()
    {
        return $this->belongsToMany(
            \App\Models\Conference::class,
            'user_manages_conferences',
            'user_id',
            'conference_id'
        );
    }

    public function canManageConference($conferenceId)
    {
        return $this->isAdmin() || $this->managedConferences()->where('conference_id', $conferenceId)->exists();
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
