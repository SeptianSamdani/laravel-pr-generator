<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PurchaseRequisition;
use App\Services\PrDocxGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrDetail extends Component
{
    use WithFileUploads;

    public $prId;
    public $pr;
    public $canEdit = false;
    public $canDelete = false;
    public $canApprove = false;
    public $canUploadPayment = false; 

    // Approval & Payment
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showPaymentModal = false;
    
    public $managerSignature;
    public $existingManagerSignature = null; // NEW
    public $rejectionNote = '';
    
    public $paymentProof;
    public $paymentDate;
    public $paymentAmount;
    public $paymentBank;
    public $paymentAccountNumber;
    public $paymentAccountName;

    public function mount($id)
    {
        $this->prId = $id;
        $this->loadPr();
        $this->checkPermissions();
    }

    public function loadPr()
    {
        $this->pr = PurchaseRequisition::with([
            'items', 
            'outlet', 
            'creator', 
            'approver',
            'invoices.uploader'
        ])->findOrFail($this->prId);

        // Check if user can view this PR
        if (!Auth::user()->hasAnyRole(['super_admin', 'admin', 'manager']) 
            && $this->pr->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    public function checkPermissions()
    {
        $user = Auth::user();

        // Can edit if: own PR, status is draft/submitted/approved, has permission
        $this->canEdit = ($this->pr->created_by === $user->id || $user->can('pr.edit'))
            && in_array($this->pr->status, ['draft', 'submitted', 'approved']);

        // Can delete if: own PR, status is draft, has permission
        $this->canDelete = ($this->pr->created_by === $user->id || $user->can('pr.delete'))
            && $this->pr->isDraft();

        // Can approve if: has permission, status is submitted, not own PR
        $this->canApprove = $user->can('pr.approve')
            && $this->pr->isSubmitted()
            && $this->pr->created_by !== $user->id;

        // Can upload payment if: manager role, status is approved
        $this->canUploadPayment = $user->can('pr.approve')
            && $this->pr->isApproved();
    }

    public function deletePr()
    {
        if (!$this->canDelete) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus PR ini');
            return;
        }

        $this->pr->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR deleted');

        session()->flash('success', 'PR berhasil dihapus');
        return redirect()->route('pr.index');
    }

    public function submitForApproval()
    {
        if (!$this->pr->isDraft()) {
            session()->flash('error', 'Hanya PR dengan status draft yang dapat disubmit');
            return;
        }

        if ($this->pr->created_by !== Auth::id() && !Auth::user()->can('pr.edit')) {
            session()->flash('error', 'Anda tidak memiliki akses untuk submit PR ini');
            return;
        }

        $this->pr->update(['status' => 'submitted']);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR submitted for approval');

        session()->flash('success', 'PR berhasil disubmit untuk approval');
        $this->loadPr();
        $this->checkPermissions();
    }

    /**
     * APPROVE WORKFLOW
     */
    public function openApproveModal()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk approve PR ini');
            return;
        }
        
        // NEW: Auto-load manager signature from profile
        $this->existingManagerSignature = Auth::user()->hasSignature() 
            ? Auth::user()->signature_path 
            : null;
        
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->managerSignature = null;
        $this->existingManagerSignature = null;
    }

    public function approvePr()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk approve PR ini');
            return;
        }

        // NEW: Validasi signature - bisa dari upload atau profile
        $hasSignature = !empty($this->managerSignature) || 
                       !empty($this->existingManagerSignature) || 
                       Auth::user()->hasSignature();

        if (!$hasSignature) {
            $this->addError('managerSignature', 'Tanda tangan diperlukan. Upload di sini atau set di Profile Anda.');
            return;
        }

        // Validate uploaded signature if provided
        if ($this->managerSignature) {
            $this->validate([
                'managerSignature' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ], [
                'managerSignature.required' => 'Signature harus di-upload',
                'managerSignature.image' => 'File harus berupa gambar',
                'managerSignature.max' => 'Ukuran file maksimal 2MB',
            ]);
        }

        try {
            // NEW: Smart signature handling
            $signaturePath = null;
            
            if ($this->managerSignature) {
                // User uploaded new signature for this approval
                $signaturePath = $this->managerSignature->store('signatures', 'public');
                
            } elseif (Auth::user()->hasSignature()) {
                // Use signature from profile
                $signaturePath = Auth::user()->signature_path;
            }

            $this->pr->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'manager_signature_path' => $signaturePath,
            ]);

            // Auto-generate DOCX after approval
            try {
                $docxService = app(PrDocxGeneratorService::class);
                $docxPath = $docxService->generateDocx($this->pr);
                
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($this->pr)
                    ->withProperties([
                        'docx_generated' => $docxPath,
                        'signature_source' => $this->managerSignature ? 'uploaded' : 'profile',
                    ])
                    ->log('PR approved with signature - DOCX auto-generated');
            } catch (\Exception $e) {
                Log::error('DOCX generation failed after approval: ' . $e->getMessage());
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($this->pr)
                ->withProperties([
                    'signature_source' => $this->managerSignature ? 'uploaded' : 'profile',
                ])
                ->log('PR approved with signature');

            session()->flash('success', 'PR berhasil disetujui');
            $this->closeApproveModal();
            $this->loadPr();
            $this->checkPermissions();

        } catch (\Exception $e) {
            Log::error('PR approval failed: ' . $e->getMessage());
            session()->flash('error', 'Gagal approve PR: ' . $e->getMessage());
        }
    }

    /**
     * REJECT WORKFLOW
     */
    public function openRejectModal()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk reject PR ini');
            return;
        }
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectionNote = '';
    }

    public function rejectPr()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk reject PR ini');
            return;
        }

        $this->validate([
            'rejectionNote' => 'required|string|min:10|max:500',
        ], [
            'rejectionNote.required' => 'Alasan penolakan harus diisi',
            'rejectionNote.min' => 'Alasan penolakan minimal 10 karakter',
        ]);

        $this->pr->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $this->rejectionNote,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR rejected');

        session()->flash('success', 'PR telah ditolak');
        $this->closeRejectModal();
        $this->loadPr();
        $this->checkPermissions();
    }

    /**
     * PAYMENT PROOF WORKFLOW
     */
    public function openPaymentModal()
    {
        if (!$this->pr->isApproved()) {
            session()->flash('error', 'PR harus approved terlebih dahulu');
            return;
        }

        if (!Auth::user()->can('pr.approve')) {
            session()->flash('error', 'Anda tidak memiliki akses');
            return;
        }

        // Pre-fill payment info from PR
        $this->paymentAmount = $this->pr->total;
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentBank = $this->pr->recipient_bank ?? '';
        $this->paymentAccountNumber = $this->pr->recipient_account_number ?? '';
        $this->paymentAccountName = $this->pr->recipient_name ?? '';
        
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset([
            'paymentProof',
            'paymentDate',
            'paymentAmount',
            'paymentBank',
            'paymentAccountNumber',
            'paymentAccountName',
        ]);
    }

    public function uploadPaymentProof()
    {
        if (!$this->pr->isApproved()) {
            session()->flash('error', 'PR harus approved terlebih dahulu');
            return;
        }

        $this->validate([
            'paymentProof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'paymentDate' => 'required|date',
            'paymentAmount' => 'required|numeric|min:0',
            'paymentBank' => 'required|string|max:100',
            'paymentAccountNumber' => 'required|string|max:50',
            'paymentAccountName' => 'required|string|max:255',
        ], [
            'paymentProof.required' => 'Bukti transfer harus di-upload',
            'paymentDate.required' => 'Tanggal transfer harus diisi',
            'paymentAmount.required' => 'Jumlah transfer harus diisi',
            'paymentBank.required' => 'Bank harus diisi',
            'paymentAccountNumber.required' => 'Nomor rekening harus diisi',
            'paymentAccountName.required' => 'Nama penerima harus diisi',
        ]);

        // Store payment proof
        $proofPath = $this->paymentProof->store('payment-proofs', 'public');

        $this->pr->update([
            'status' => 'paid',
            'payment_date' => $this->paymentDate,
            'payment_amount' => $this->paymentAmount,
            'payment_bank' => $this->paymentBank,
            'payment_account_number' => $this->paymentAccountNumber,
            'payment_account_name' => $this->paymentAccountName,
            'payment_proof_path' => $proofPath,
            'payment_uploaded_at' => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('Payment proof uploaded');

        session()->flash('success', 'Bukti transfer berhasil di-upload. Status PR: PAID');
        $this->closePaymentModal();
        $this->loadPr();
        $this->checkPermissions();
    }

    // NEW: Download Staff Signature
    public function downloadStaffSignature()
    {
        if (!$this->pr->hasStaffSignature()) {
            session()->flash('error', 'Tanda tangan staff tidak tersedia');
            return;
        }
        return Storage::disk('public')->download($this->pr->staff_signature_path);
    }

    // Download Manager Signature
    public function downloadManagerSignature()
    {
        if (!$this->pr->hasManagerSignature()) {
            session()->flash('error', 'Tanda tangan manager tidak tersedia');
            return;
        }
        return Storage::disk('public')->download($this->pr->manager_signature_path);
    }

    // Download Payment Proof
    public function downloadPaymentProof()
    {
        if (!$this->pr->hasPaymentProof()) {
            session()->flash('error', 'Bukti transfer tidak tersedia');
            return;
        }
        return Storage::disk('public')->download($this->pr->payment_proof_path);
    }

    // NEW: Download invoice
    public function downloadInvoice($invoiceId)
    {
        $invoice = $this->pr->invoices()->findOrFail($invoiceId);
        return Storage::disk('public')->download($invoice->file_path, $invoice->file_name);
    }

    public function render()
    {
        return view('livewire.pr-detail')->layout('components.layouts.app', ['title' => 'Detail Purchase Requisitions']);
    }
}