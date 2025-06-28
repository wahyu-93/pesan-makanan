<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['code','name','external_id','checkout_link','barcodes_id','payment_method','payment_status','subtotal','ppn','total'];

    public function barcode()
    {
        return $this->belongsTo(Barcode::class, 'barcodes_id','id');
    }

    public function transactionItem()
    {
        return $this->hasMany(TransactionItem::class, 'id','transactions_id');
    }
}
