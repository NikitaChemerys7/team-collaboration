<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'date',
        'location',
        'description',
        'hero_image',
        'gallery',
        'files',
        'speakers'
    ];

    protected $casts = [
        'date' => 'date',
        'gallery' => 'array',
        'files' => 'array',
        'speakers' => 'array'
    ];

    public function subpages()
    {
        return $this->hasMany(Subpage::class)->orderBy('order');
    }

    public function editors()
    {
        return $this->belongsToMany(
            User::class,
            'user_manages_conferences',
            'conference_id',
            'user_id'
        )->withTimestamps()
        ->withPivot(['granted_by_user_id', 'granted_at']);
    }
}
