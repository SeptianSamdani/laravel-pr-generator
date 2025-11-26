<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PrDetail extends Component
{
    public $prId;
    public $pr;
    public $canEdit = false;
    public $canDelete = false;
    public $canApprove = false;

    public function mount($id)
    {
        $this->prId = $id;
        $this->loadPr();
        $this->checkPermissions();
    }

    public function loadPr()
    {
        $this->pr = PurchaseRequisition::with(['items', 'outlet', 'creator', 'approver'])
            ->findOrFail($this->prId);

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
            && $this->pr->status === 'draft';

        // Can delete if: own PR, status is draft, has permission
        $this->canDelete = ($this->pr->created_by === $user->id || $user->can('pr.delete'))
            && $this->pr->status === 'draft';

        // Can approve if: has permission, status is submitted, not own PR
        $this->canApprove = $user->can('pr.approve')
            && $this->pr->status === 'submitted'
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

    public function downloadPdf()
    {
        // Load PR with relationships
        $pr = PurchaseRequisition::with(['items', 'outlet', 'creator', 'approver'])
            ->findOrFail($this->prId);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.pr-template', ['pr' => $pr])
            ->setPaper('a4', 'portrait');

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR PDF downloaded');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'PR-' . $pr->pr_number . '.pdf');
    }

    public function submitForApproval()
    {
        if ($this->pr->status !== 'draft') {
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

    public function approvePr()
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk approve PR ini');
            return;
        }

        $this->pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR approved');

        session()->flash('success', 'PR berhasil disetujui');
        $this->loadPr();
        $this->checkPermissions();
    }

    public function rejectPr($reason)
    {
        if (!$this->canApprove) {
            session()->flash('error', 'Anda tidak memiliki akses untuk reject PR ini');
            return;
        }

        $this->pr->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $reason,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->pr)
            ->log('PR rejected');

        session()->flash('success', 'PR telah ditolak');
        $this->loadPr();
        $this->checkPermissions();
    }

    public function render()
    {
        return view('livewire.pr-detail')->layout('components.layouts.app');
    }
}