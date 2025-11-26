<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrApproval extends Component
{
    use WithPagination;

    public $search = '';
    public $outletFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    // Bulk selection
    public $selectedPrs = [];
    public $selectAll = false;

    // Rejection modal
    public $showRejectModal = false;
    public $rejectingPrId = null;
    public $rejectionNote = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'outletFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOutletFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPrs = $this->getPendingPrs()->pluck('id')->toArray();
        } else {
            $this->selectedPrs = [];
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->outletFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    private function getPendingPrs()
    {
        return PurchaseRequisition::with(['outlet', 'creator'])
            ->where('status', 'submitted')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('pr_number', 'like', '%' . $this->search . '%')
                        ->orWhere('perihal', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->outletFilter, function ($q) {
                $q->where('outlet_id', $this->outletFilter);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('tanggal', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('tanggal', '<=', $this->dateTo);
            })
            ->latest()
            ->get();
    }

    public function approvePr($id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        // Authorization check
        if (!Auth::user()->can('pr.approve')) {
            session()->flash('error', 'Anda tidak memiliki akses untuk approve PR');
            return;
        }

        // Can't approve own PR
        if ($pr->created_by === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat approve PR sendiri');
            return;
        }

        // Must be submitted status
        if ($pr->status !== 'submitted') {
            session()->flash('error', 'Hanya PR dengan status submitted yang dapat diapprove');
            return;
        }

        $pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR approved');

        session()->flash('success', "PR {$pr->pr_number} berhasil disetujui");
    }

    public function openRejectModal($id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        // Authorization check
        if (!Auth::user()->can('pr.approve')) {
            session()->flash('error', 'Anda tidak memiliki akses untuk reject PR');
            return;
        }

        // Can't reject own PR
        if ($pr->created_by === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat reject PR sendiri');
            return;
        }

        $this->rejectingPrId = $id;
        $this->rejectionNote = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingPrId = null;
        $this->rejectionNote = '';
    }

    public function rejectPr()
    {
        $this->validate([
            'rejectionNote' => 'required|string|min:10|max:500',
        ], [
            'rejectionNote.required' => 'Alasan penolakan harus diisi',
            'rejectionNote.min' => 'Alasan penolakan minimal 10 karakter',
            'rejectionNote.max' => 'Alasan penolakan maksimal 500 karakter',
        ]);

        $pr = PurchaseRequisition::findOrFail($this->rejectingPrId);

        // Must be submitted status
        if ($pr->status !== 'submitted') {
            session()->flash('error', 'Hanya PR dengan status submitted yang dapat direject');
            $this->closeRejectModal();
            return;
        }

        $pr->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $this->rejectionNote,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR rejected');

        session()->flash('success', "PR {$pr->pr_number} telah ditolak");
        $this->closeRejectModal();
    }

    public function bulkApprove()
    {
        if (empty($this->selectedPrs)) {
            session()->flash('error', 'Pilih minimal 1 PR untuk diapprove');
            return;
        }

        DB::transaction(function () {
            $prs = PurchaseRequisition::whereIn('id', $this->selectedPrs)
                ->where('status', 'submitted')
                ->where('created_by', '!=', Auth::id())
                ->get();

            foreach ($prs as $pr) {
                $pr->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($pr)
                    ->log('PR bulk approved');
            }
        });

        session()->flash('success', count($this->selectedPrs) . ' PR berhasil disetujui');
        $this->selectedPrs = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $pendingPrs = PurchaseRequisition::with(['outlet', 'creator'])
            ->where('status', 'submitted')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('pr_number', 'like', '%' . $this->search . '%')
                        ->orWhere('perihal', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->outletFilter, function ($q) {
                $q->where('outlet_id', $this->outletFilter);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('tanggal', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('tanggal', '<=', $this->dateTo);
            })
            ->latest()
            ->paginate($this->perPage);

        $outlets = \App\Models\Outlet::where('is_active', true)->get();

        // Statistics
        $stats = [
            'pending' => PurchaseRequisition::where('status', 'submitted')->count(),
            'approved_today' => PurchaseRequisition::where('status', 'approved')
                ->whereDate('approved_at', today())
                ->count(),
            'total_amount' => PurchaseRequisition::where('status', 'submitted')->sum('total'),
        ];

        return view('livewire.pr-approval', [
            'pendingPrs' => $pendingPrs,
            'outlets' => $outlets,
            'stats' => $stats,
        ])->layout('components.layouts.app');
    }
}