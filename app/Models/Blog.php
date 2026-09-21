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
        'content' => 'array',
        'faqs' => 'array',
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
