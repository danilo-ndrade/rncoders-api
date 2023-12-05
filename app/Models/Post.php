<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'published_at'
    ];

    /**
     * Get the user that owns the post.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function category() {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the file that owns the post.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}