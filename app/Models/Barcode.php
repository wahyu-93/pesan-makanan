<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = ['table_number', 'images','qr_code', 'users_id'];

    protected static function booted(): void
    {
        static::deleting(function ($barcode) {
            // Hapus file dari storage
            if ($barcode->images) {
                Storage::disk('public')->delete($barcode->images);
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id','id');
    }

    public function getImageUrlAttribute()
    {
        return $this->images ? asset('storage/' . $this->images) : null;
    }
}
