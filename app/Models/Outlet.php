<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get purchase requisitions for this outlet
     */
    public function purchaseRequisitions()
    {
        return $this->hasMany(PurchaseRequisition::class);
    }

    /**
     * Scope for active outlets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}