<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseRequisition;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $dateRange = 'this_month';
    public $startDate;
    public $endDate;
    public $selectedOutlet = '';
    
    // NEW: Dashboard mode
    public $dashboardMode = 'staff'; // 'staff' or 'manager'

    public function mount()
    {
        $this->setDateRange('this_month');
        
        // NEW: Determine dashboard mode based on user role
        $this->dashboardMode = Auth::user()->hasAnyRole(['super_admin', 'admin', 'manager']) 
            ? 'manager' 
            : 'staff';
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;

        switch ($range) {
            case 'today':
                $this->startDate = now()->startOfDay();
                $this->endDate = now()->endOfDay();
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek();
                $this->endDate = now()->endOfWeek();
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth();
                $this->endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth();
                $this->endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear();
                $this->endDate = now()->endOfYear();
                break;
        }
    }

    public function updatedDateRange()
    {
        if ($this->dateRange !== 'custom') {
            $this->setDateRange($this->dateRange);
        }
    }

    private function getBaseQuery()
    {
        $query = PurchaseRequisition::query();

        // NEW: Filter berdasarkan dashboard mode
        if ($this->dashboardMode === 'staff') {
            // Staff: HANYA lihat PR yang dibuat sendiri
            $query->where('created_by', Auth::id());
        } else {
            // Manager/Admin: Lihat SEMUA PR
            // Jika ada filter outlet, apply filter
            if ($this->selectedOutlet) {
                $query->where('outlet_id', $this->selectedOutlet);
            }
        }

        return $query;
    }

    public function getStatsProperty()
    {
        $query = $this->getBaseQuery();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        $totalPRs = $query->count();
        $totalAmount = $query->sum('total');

        // Status counts
        $submittedCount = (clone $query)->where('status', 'submitted')->count();
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $paidCount = (clone $query)->where('status', 'paid')->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')->count();
        $draftCount = (clone $query)->where('status', 'draft')->count();

        // Previous period comparison
        $previousQuery = $this->getBaseQuery();
        if ($this->startDate && $this->endDate) {
            $daysDiff = $this->startDate->diffInDays($this->endDate);
            $previousStart = $this->startDate->copy()->subDays($daysDiff + 1);
            $previousEnd = $this->endDate->copy()->subDays($daysDiff + 1);
            $previousQuery->whereBetween('created_at', [$previousStart, $previousEnd]);
        }

        $previousTotal = $previousQuery->count();
        $percentageChange = $previousTotal > 0 
            ? round((($totalPRs - $previousTotal) / $previousTotal) * 100, 1)
            : 0;

        return [
            'total_prs' => $totalPRs,
            'total_amount' => $totalAmount,
            'pending' => $submittedCount,
            'approved' => $approvedCount,
            'paid' => $paidCount,
            'rejected' => $rejectedCount,
            'draft' => $draftCount,
            'percentage_change' => $percentageChange,
            'approved_today' => $this->dashboardMode === 'manager' 
                ? PurchaseRequisition::where('status', 'approved')
                    ->whereDate('approved_at', today())
                    ->count()
                : 0, // Staff tidak perlu data ini
            'paid_today' => $this->dashboardMode === 'manager'
                ? PurchaseRequisition::where('status', 'paid')
                    ->whereDate('payment_uploaded_at', today())
                    ->count()
                : 0, // Staff tidak perlu data ini
        ];
    }

    public function getRecentPRsProperty()
    {
        return $this->getBaseQuery()
            ->with(['outlet', 'creator', 'approver', 'invoices'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getMonthlyChartDataProperty()
    {
        $data = [];
        
        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $query = PurchaseRequisition::query();
            
            // NEW: Apply user filter
            if ($this->dashboardMode === 'staff') {
                $query->where('created_by', Auth::id());
            }

            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            $data[] = [
                'month' => $date->format('M'),
                'total' => $query->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'paid' => (clone $query)->where('status', 'paid')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
                'submitted' => (clone $query)->where('status', 'submitted')->count(),
            ];
        }

        return $data;
    }

    public function getTopOutletsProperty()
    {
        // NEW: Top outlets hanya untuk Manager
        if ($this->dashboardMode === 'staff') {
            return collect(); // Return empty collection untuk staff
        }

        return PurchaseRequisition::query()
            ->select('outlet_id', DB::raw('COUNT(*) as total_prs'), DB::raw('SUM(total) as total_amount'))
            ->with('outlet')
            ->groupBy('outlet_id')
            ->orderByDesc('total_prs')
            ->limit(5)
            ->get();
    }

    public function getStatusDistributionProperty()
    {
        $query = $this->getBaseQuery();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return [
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'paid' => (clone $query)->where('status', 'paid')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
    }

    public function getPendingApprovalsProperty()
    {
        // NEW: Pending approvals hanya untuk Manager
        if ($this->dashboardMode === 'staff') {
            return collect(); // Return empty collection untuk staff
        }

        if (!Auth::user()->can('pr.approve')) {
            return collect();
        }

        return PurchaseRequisition::with(['outlet', 'creator', 'invoices'])
            ->where('status', 'submitted')
            ->where('created_by', '!=', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
    $outlets = Outlet::where('is_active', true)->get();

    return view('livewire.dashboard', [
        'title' => 'Dashboard',
        'stats' => $this->stats,
        'recentPRs' => $this->recentPRs,
        'monthlyChartData' => $this->monthlyChartData,
        'topOutlets' => $this->topOutlets,
        'statusDistribution' => $this->statusDistribution,
        'pendingApprovals' => $this->pendingApprovals,
        'outlets' => $outlets,
        'dashboardMode' => $this->dashboardMode,
    ])->layout('components.layouts.app');
    }
}