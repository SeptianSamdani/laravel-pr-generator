<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/PurchaseRequisition.php
class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number', 'tanggal', 'perihal', 'alasan', 
        'outlet_id', 'total', 'status', 'created_by', 
        'approved_by', 'approved_at', 'rejection_note'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'total' => 'decimal:2'
    ];

    public function items() {
        return $this->hasMany(PrItem::class)->orderBy('order');
    }

    public function outlet() {
        return $this->belongsTo(Outlet::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($pr) {
            $pr->pr_number = 'PR-' . date('Ymd') . '-' . str_pad(
                static::whereDate('created_at', today())->count() + 1, 
                4, '0', STR_PAD_LEFT
            );
        });
    }
}
