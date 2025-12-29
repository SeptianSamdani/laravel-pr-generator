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
        'staff_signature_path',
        'payment_date',
        'payment_amount',
        'payment_bank',
        'payment_account_number',
        'payment_account_name',
        'payment_proof_path',
        'payment_uploaded_at',
        'recipient_name',
        'recipient_bank',
        'recipient_account_number',
        'recipient_phone',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_date' => 'date',
        'total' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

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

    // Relations
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

    // Helper methods untuk nama
    public function getStaffNameAttribute(): string
    {
        return $this->creator->name ?? '-';
    }

    public function getManagerNameAttribute(): string
    {
        return $this->approver->name ?? '-';
    }

    // Scopes
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

    // Status checks
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
        return $this->hasStaffSignature() && $this->hasManagerSignature();
    }

    public function hasStaffSignature(): bool
    {
        return !empty($this->staff_signature_path);
    }

    public function hasManagerSignature(): bool
    {
        return !empty($this->manager_signature_path);
    }

    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof_path);
    }

    public function hasRecipientInfo(): bool
    {
        return !empty($this->recipient_name) 
            && !empty($this->recipient_bank) 
            && !empty($this->recipient_account_number);
    }

    public function isFullyCompleted(): bool
    {
        return $this->isPaid() 
            && $this->hasStaffSignature() 
            && $this->hasManagerSignature() 
            && $this->hasPaymentProof()
            && $this->hasRecipientInfo();
    }
}