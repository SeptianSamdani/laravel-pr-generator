<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrDetail extends Component
{
    use WithFileUploads;

    public $prId;
    public $pr;
    public $canEdit = false;
    public $canDelete = false;
    public $canApprove = false;

    // NEW: Approval & Payment
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showPaymentModal = false;
    
    public $managerSignature;
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

        // Can edit if: own PR, status is draft, has permission
        $this->canEdit = ($this->pr->created_by === $user->id || $user->can('pr.edit'))
            && $this->pr->isDraft();

        // Can delete if: own PR, status is draft, has permission
        $this->canDelete = ($this->pr->created_by === $user->id || $user->can('pr.delete'))
            && $this->pr->isDraft();

        // Can approve if: has permission, status is submitted, not own PR
        $this->canApprove = $user->can('pr.approve')
            && $this->pr->isSubmitted()
            && $this->pr->created_by !== $user->id;
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
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->managerSignature = null;
    }

    public function approvePr()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk approve PR ini');
            return;
        }

        $this->validate([
            'managerSignature' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'managerSignature.required' => 'Signature harus di-upload',
            'managerSignature.image' => 'File harus berupa gambar',
            'managerSignature.max' => 'Ukuran file maksimal 2MB',
        ]);

        // Store signature
        $signaturePath = $this->managerSignature->store('public/signatures');

        $this->pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'manager_signature_path' => $signaturePath,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR approved with signature');

        session()->flash('success', 'PR berhasil disetujui');
        $this->closeApproveModal();
        $this->loadPr();
        $this->checkPermissions();
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

        // Pre-fill payment amount with PR total
        $this->paymentAmount = $this->pr->total;
        $this->paymentDate = now()->format('Y-m-d');
        
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
        $proofPath = $this->paymentProof->store('public/payment-proofs');

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

    public function downloadFile($type)
    {
        switch ($type) {
            case 'signature':
                if (!$this->pr->hasSignature()) {
                    session()->flash('error', 'Signature tidak tersedia');
                    return;
                }
                return Storage::download($this->pr->manager_signature_path);

            case 'payment':
                if (!$this->pr->hasPaymentProof()) {
                    session()->flash('error', 'Bukti transfer tidak tersedia');
                    return;
                }
                return Storage::download($this->pr->payment_proof_path);
        }
    }

    public function render()
    {
        return view('livewire.pr-detail')->layout('components.layouts.app');
    }
}