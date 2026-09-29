<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'subtitle',          // kept as DB column name; labeled "Excerpt" in the UI
        'image',
        'alt_text',
        'content',
        'faqs',
        'schema_markup',
        'is_active',
        'publish_at',
        'meta_title',
        'meta_description',
        'canonical_url',
    ];

    protected $casts = [
        'content'        => 'array',
        'faqs'           => 'array',
        'schema_markup'  => 'array',
        'is_active'      => 'boolean',
        'publish_at'     => 'datetime',
    ];

    /**
     * Hide the raw image path from the API response.
     */
    protected $hidden = ['image'];

    /**
     * Append image_url to every serialized response.
     */
    protected $appends = ['image_url'];

    /**
     * Auto-generate a unique slug from the title on create.
     * On update, only regenerate if the slug has been explicitly cleared.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug) && $blog->title) {
                $blog->slug = static::generateUniqueSlug(
                    strip_tags($blog->title)
                );
            }
        });

        static::updating(function ($blog) {
            if (empty($blog->slug) && $blog->title) {
                $blog->slug = static::generateUniqueSlug(
                    strip_tags($blog->title),
                    $blog->id
                );
            }
        });
    }

    public static function generateUniqueSlug(string $text, ?int $excludeId = null): string
    {
        $base = Str::slug(strip_tags($text));
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Returns the full public URL for the blog image.
     * DB stores: "public_storage/blogs/filename.jpg"
     * API returns: "APP_URL/storage/blogs/filename.jpg"
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Strip prefix → /storage/blogs/filename.jpg
        $diskPath = ltrim(str_replace('public_storage/', '', $this->image), '/');
        return rtrim(config('app.url'), '/') . '/public_storage/' . $diskPath;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
