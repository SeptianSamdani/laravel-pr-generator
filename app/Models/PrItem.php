<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/PrItem.php
class PrItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_requisition_id', 'order', 'nama_item', 
        'jumlah', 'satuan', 'harga', 'subtotal'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function purchaseRequisition() {
        return $this->belongsTo(PurchaseRequisition::class);
    }
}