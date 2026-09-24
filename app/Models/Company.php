<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'api_key'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($company) {
            if (empty($company->api_key)) {
                $company->api_key = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}
