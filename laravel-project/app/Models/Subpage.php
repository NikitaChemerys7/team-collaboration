<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subpage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'title',
        'slug',
        'content',
        'order',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_manages_subpages')
            ->withTimestamps()
            ->withPivot(['granted_by_user_id', 'granted_at']);
    }
}
