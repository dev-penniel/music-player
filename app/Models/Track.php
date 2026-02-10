<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'title', 'artist', 'album', 'file_path', 'cover_path', 'duration'
    ];

    // Helper to get a public URL
    public function fileUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function coverUrl()
    {
        return $this->cover_path ? asset('storage/' . $this->cover_path) : asset('images/default-cover.jpg');
    }
}
