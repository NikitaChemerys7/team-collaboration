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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'degree',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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

    public function managedConferences()
    {
        return $this->belongsToMany(
            \App\Models\Conference::class,
            'user_manages_conferences',
            'user_id',
            'conference_id'
        );
    }

    public function managedYears()
    {
        return $this->hasMany(\App\Models\UserManagesYear::class);
    }

    public function canManageYear($year)
    {
        return $this->isAdmin() || $this->managedYears()->where('year', $year)->exists();
    }

    public function canManageConference($conferenceId)
    {
        if ($this->isAdmin()) return true;
        
        $conference = \App\Models\Conference::find($conferenceId);
        if (!$conference) return false;
        
        return $this->canManageYear($conference->year);
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
