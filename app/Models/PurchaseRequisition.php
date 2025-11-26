<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number', 
        'tanggal', 
        'perihal', 
        'alasan', 
        'outlet_id', 
        'total', 
        'status', 
        'created_by', 
        'approved_by', 
        'approved_at', 
        'rejection_note',
        'manager_signature_path',
        'payment_date',
        'payment_amount',
        'payment_bank',
        'payment_account_number',
        'payment_account_name',
        'payment_proof_path',
        'payment_uploaded_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_date' => 'date',
        'total' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Boot method - Auto generate PR number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pr) {
            $pr->pr_number = 'PR-' . date('Ymd') . '-' . str_pad(
                static::whereDate('created_at', today())->count() + 1, 
                4, '0', STR_PAD_LEFT
            );
        });
    }

    /**
     * Relations
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrItem::class)->orderBy('order');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(PrInvoice::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Helper methods
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft() && $this->items()->count() > 0;
    }

    public function canBeApproved(): bool
    {
        return $this->isSubmitted();
    }

    public function hasSignature(): bool
    {
        return !empty($this->manager_signature_path);
    }

    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof_path);
    }

    public function isFullyCompleted(): bool
    {
        return $this->isPaid() && $this->hasSignature() && $this->hasPaymentProof();
    }
}