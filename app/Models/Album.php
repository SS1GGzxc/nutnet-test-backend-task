<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author',
        'description',
        'img',
    ];

    public function logs()
    {
        return $this->hasMany(AlbumLog::class);
    }
}
