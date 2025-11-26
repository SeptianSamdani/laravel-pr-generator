<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;

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

        // Check authorization
        if (!Auth::user()->can('pr.delete') && $pr->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus PR ini');
            return;
        }

        // Can only delete draft
        if (!$pr->isDraft()) {
            session()->flash('error', 'Hanya PR dengan status draft yang dapat dihapus');
            return;
        }

        $pr->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR deleted');

        session()->flash('success', 'PR berhasil dihapus');
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
        ])->layout('components.layouts.app'); 
    }
}