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
        Schema::create('pr_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')
                  ->constrained('purchase_requisitions')
                  ->cascadeOnDelete();
            
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // image/jpeg, application/pdf, etc
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
            
            // Index
            $table->index('purchase_requisition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_invoices');
    }
};