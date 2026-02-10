<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{

    protected $fillable = [
        'artist_id',
        'title',
        'slug',
        'cover_image',
        'type',
        'release_date',
        'is_published',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }
}
