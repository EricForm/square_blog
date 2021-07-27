<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'reading_time', 'img',
    ];
}