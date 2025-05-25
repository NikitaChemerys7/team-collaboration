<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subpage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'title',
        'content',
        'slug',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
