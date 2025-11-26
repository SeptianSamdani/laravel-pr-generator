<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique();
            $table->date('tanggal');
            $table->string('perihal');
            $table->text('alasan')->nullable();

            // Relations
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Total & Status
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            
            // Approval Info
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            
            // NEW: Signature Manager (scan TTD/stempel)
            $table->string('manager_signature_path')->nullable();
            
            // NEW: Payment Info
            $table->date('payment_date')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->string('payment_bank')->nullable();
            $table->string('payment_account_number')->nullable();
            $table->string('payment_account_name')->nullable();
            $table->string('payment_proof_path')->nullable(); // Bukti transfer
            $table->timestamp('payment_uploaded_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};