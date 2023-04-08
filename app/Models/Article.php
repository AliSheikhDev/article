<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ["title", "content", "slug", "user_id", "category_id", "cover_path", "online"];

    public function getRouteKeyName()
    {
        return "slug";
    }

    /**
     * Relationship between a article with its creator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * Relationship between a article with a category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship article with tags
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     *  Relationship between a article with comments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

	/**
     *  Relationship between a article with comments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class)->latest();
    }

    public function getDescriptionAttribute()
    {
        return substr($this->content, 0, 70) . "...";
    }

    public function scopeSearch($query, $value)
    {
        return $query->where("title", "LIKE", "%$value%");
    }

    public function getCoverAttribute()
    {
        $parts = explode("/", $this->cover_path);

        return end($parts);
    }

    public function scopeOnline($query, $condition = true)
    {
        return $query->where(["online" => $condition]);
    }

}
