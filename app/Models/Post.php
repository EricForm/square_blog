<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'reading_time', 'img',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');
    }
}