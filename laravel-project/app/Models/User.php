<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function managedConferences(): BelongsToMany
    {
        return $this->belongsToMany(
            Conference::class,
            'user_manages_conferences',
            'user_id',
            'conference_id'
        )
        ->withTimestamps()
        ->withPivot(['granted_by_user_id', 'granted_at']);
    }

    public function canManageConference($conferenceId)
    {
        return $this->isAdmin() || $this->managedConferences()->where('conference_id', $conferenceId)->exists();
    }

    public function managedSubpages(): BelongsToMany
    {
        return $this->belongsToMany(
            Subpage::class,
            'user_manages_subpages',
            'user_id',
            'subpage_id'
        )
        ->withTimestamps()
        ->withPivot(['granted_by_user_id', 'granted_at']);
    }


    public function canManageSubpage(int $subpageId): bool
    {
        return $this->isAdmin()
            || $this->managedSubpages()->where('id', $subpageId)->exists()
            || $this->managedConferences()
                    ->whereHas('subpages', function ($query) use ($subpageId) {
                        $query->where('id', $subpageId);
                    })->exists();
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
