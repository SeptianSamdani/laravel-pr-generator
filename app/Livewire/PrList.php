<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $outletFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'outletFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingOutletFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->outletFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function deletePr($id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        $user = Auth::user();
        
        // Check authorization
        $isManager = $user->hasAnyRole(['super_admin', 'admin', 'manager']) 
                   && $user->can('pr.delete');
        
        $isOwner = ($pr->created_by === $user->id) 
                 && $user->can('pr.delete');
        
        if (!$isManager && !$isOwner) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses untuk menghapus PR ini');
            return;
        }

        // NEW: Deletion rules
        if ($isManager) {
            // Manager can delete Draft, Submitted, Approved, Rejected
            // But NOT Paid (financial record must be kept)
            if ($pr->isPaid()) {
                $this->dispatch('notify', type: 'error', message: 'PR dengan status Paid tidak dapat dihapus (financial record harus disimpan)');
                return;
            }
        } else if ($isOwner) {
            // NEW: Staff can delete their own PR with ANY status
            // No status restriction for owner
            // They created it, they can delete it
        }

        try {
            // Delete related files
            // 1. Delete invoices
            if ($pr->invoices()->count() > 0) {
                foreach ($pr->invoices as $invoice) {
                    if (Storage::disk('public')->exists($invoice->file_path)) {
                        Storage::disk('public')->delete($invoice->file_path);
                    }
                }
                $pr->invoices()->delete();
            }

            // 2. Delete staff signature (if not from profile)
            if ($pr->staff_signature_path 
                && $pr->creator
                && $pr->staff_signature_path !== $pr->creator->signature_path
                && Storage::disk('public')->exists($pr->staff_signature_path)) {
                Storage::disk('public')->delete($pr->staff_signature_path);
            }

            // 3. Delete manager signature (if not from profile)
            if ($pr->manager_signature_path 
                && $pr->approver
                && $pr->manager_signature_path !== $pr->approver->signature_path
                && Storage::disk('public')->exists($pr->manager_signature_path)) {
                Storage::disk('public')->delete($pr->manager_signature_path);
            }

            // 4. Delete payment proof (if exists)
            if ($pr->payment_proof_path 
                && Storage::disk('public')->exists($pr->payment_proof_path)) {
                Storage::disk('public')->delete($pr->payment_proof_path);
            }

            // 5. Delete PR items
            $pr->items()->delete();

            // 6. Delete PR
            $prNumber = $pr->pr_number;
            $prStatus = $pr->status;
            $pr->delete();

            // 7. Log activity (non-critical — jangan batalkan delete jika gagal)
            try {
                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'pr_number'       => $prNumber,
                        'deleted_by_role' => $user->roles->pluck('name')->first(),
                        'status'          => $prStatus,
                        'is_owner'        => $isOwner,
                    ])
                    ->log('PR deleted');
            } catch (\Exception $logEx) {
                \Log::warning('Activity log failed on PR delete: ' . $logEx->getMessage());
            }

            session()->flash('success', "PR {$prNumber} (status: {$prStatus}) berhasil dihapus");

        } catch (\Exception $e) {
            \Log::error('Delete PR failed: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus PR: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = PurchaseRequisition::with(['outlet', 'creator', 'approver', 'invoices'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('pr_number', 'like', '%' . $this->search . '%')
                        ->orWhere('perihal', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->outletFilter, function ($q) {
                $q->where('outlet_id', $this->outletFilter);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('tanggal', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('tanggal', '<=', $this->dateTo);
            });

        // If not admin/manager, only show own PRs
        if (!Auth::user()->hasAnyRole(['super_admin', 'admin', 'manager'])) {
            $query->where('created_by', Auth::id());
        }

        $purchaseRequisitions = $query->latest()->paginate($this->perPage);

        $outlets = \App\Models\Outlet::where('is_active', true)->get();

        return view('livewire.pr-list', [
            'purchaseRequisitions' => $purchaseRequisitions,
            'outlets' => $outlets,
        ])->layout('components.layouts.app', ['title' => 'List Purchase Requisitions']);
    }
}