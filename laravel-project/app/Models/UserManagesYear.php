<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserManagesYear extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'granted_by_user_id',
        'granted_at'
    ];

    protected $casts = [
        'granted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }
} 