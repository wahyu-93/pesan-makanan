<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Food extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'images', 'price', 'price_afterdiscount', 'discount', 'is_promo', 'categories_id'];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->images ? asset('storage/' . $this->images) : null;
    }

    protected static function booted(): void
    {
        static::deleting(function ($food) {
            // Hapus file dari storage
            if ($food->images) {
                Storage::disk('public')->delete($food->images);
            }
        });
    }
}
