<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PrInvoice extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    /**
     * Relations
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Helper methods
     */
    public function getFileUrl(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }


    /**
     * Delete file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($invoice) {
            if (Storage::exists($invoice->file_path)) {
                Storage::delete($invoice->file_path);
            }
        });
    }
}