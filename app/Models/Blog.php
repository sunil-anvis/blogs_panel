<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'subtitle',
        'image',
        'content',
        'faqs',
        'is_active',
        'publish_at',
    ];

    protected $casts = [
        'content'    => 'array',
        'faqs'       => 'array',
        'is_active'  => 'boolean',
        'publish_at' => 'datetime',
    ];

    /**
     * Append image_url to every serialized response.
     */
    protected $appends = ['image_url'];

    /**
     * Returns the full public URL for the blog image.
     * The `image` column stores only the relative path e.g. "blogs/abc.jpg".
     * APP_URL in .env must be set to the server base URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If already a full URL, return as-is
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // DB stores: "public_storage/blogs/filename.jpg"
        // Public URL: "APP_URL/storage/blogs/filename.jpg"
        $relativePath = str_replace('public_storage/', 'storage/', $this->image);

        return rtrim(config('app.url'), '/') . '/' . $relativePath;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

