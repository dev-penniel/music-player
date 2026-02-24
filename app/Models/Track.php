<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'artist_id',
        'album_id',
        'title',
        'slug',
        'track_number',
        'duration',
        'file_path',
        'cover_path',
        'release_date',
        'is_published',
        'plays',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Main Artist
    public function mainArtist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    // Featured / Producers / etc
    public function artists()
    {
        return $this->belongsToMany(Artist::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Album (if you have Album model)
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function fileUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function coverUrl()
    {
        return $this->cover_path
            ? asset('storage/' . $this->cover_path)
            : asset('images/default-cover.jpg');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}