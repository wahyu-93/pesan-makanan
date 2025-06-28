<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = ['table_number', 'images','qr_code', 'users_id'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
