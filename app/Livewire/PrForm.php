<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PrForm extends Component
{
    public $prId;
    public $tanggal;
    public $perihal;
    public $alasan;
    public $outlet_id;
    public $status = 'draft';
    
    public $items = [];
    public $total = 0;
    
    public $outlets = [];
    public $isEdit = false;

    protected $rules = [
        'tanggal' => 'required|date',
        'perihal' => 'required|string|max:255',
        'alasan' => 'nullable|string|max:500',
        'outlet_id' => 'required|exists:outlets,id',
        'items.*.nama_item' => 'required|string|max:255',
        'items.*.jumlah' => 'required|integer|min:1',
        'items.*.satuan' => 'required|string|max:50',
        'items.*.harga' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'tanggal.required' => 'Tanggal harus diisi',
        'perihal.required' => 'Perihal harus diisi',
        'outlet_id.required' => 'Outlet harus dipilih',
        'items.*.nama_item.required' => 'Nama item harus diisi',
        'items.*.jumlah.required' => 'Jumlah harus diisi',
        'items.*.jumlah.min' => 'Jumlah minimal 1',
        'items.*.satuan.required' => 'Satuan harus diisi',
        'items.*.harga.required' => 'Harga harus diisi',
        'items.*.harga.min' => 'Harga tidak boleh negatif',
    ];

    public function mount($id = null)
    {
        $this->outlets = Outlet::where('is_active', true)->get();
        $this->tanggal = now()->format('Y-m-d');

        if ($id) {
            $this->isEdit = true;
            $this->prId = $id;
            $this->loadPurchaseRequisition($id);
        } else {
            $this->addItem();
        }
    }

    public function loadPurchaseRequisition($id)
    {
        $pr = PurchaseRequisition::with('items')->findOrFail($id);

        // Check authorization
        if (!Auth::user()->can('pr.edit') && $pr->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Can't edit if not draft
        if ($pr->status !== 'draft') {
            abort(403, 'Hanya PR dengan status draft yang dapat diedit');
        }

        $this->tanggal = $pr->tanggal->format('Y-m-d');
        $this->perihal = $pr->perihal;
        $this->alasan = $pr->alasan;
        $this->outlet_id = $pr->outlet_id;
        $this->status = $pr->status;

        $this->items = $pr->items->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_item' => $item->nama_item,
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan,
                'harga' => $item->harga,
                'subtotal' => $item->subtotal,
            ];
        })->toArray();

        $this->calculateTotal();
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => null,
            'nama_item' => '',
            'jumlah' => 1,
            'satuan' => '',
            'harga' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function updatedItems($value, $key)
    {
        // Parse key to get index and field
        $parts = explode('.', $key);
        $index = $parts[0];

        if (isset($this->items[$index])) {
            $item = &$this->items[$index];
            
            // Calculate subtotal when jumlah or harga changes
            $jumlah = (float) ($item['jumlah'] ?? 0);
            $harga = (float) ($item['harga'] ?? 0);
            $item['subtotal'] = $jumlah * $harga;

            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total = array_reduce($this->items, function ($carry, $item) {
            return $carry + ($item['subtotal'] ?? 0);
        }, 0);
    }

    public function saveDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    public function submitForApproval()
    {
        $this->status = 'submitted';
        $this->save();
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            if ($this->isEdit) {
                $pr = PurchaseRequisition::findOrFail($this->prId);
                $pr->update([
                    'tanggal' => $this->tanggal,
                    'perihal' => $this->perihal,
                    'alasan' => $this->alasan,
                    'outlet_id' => $this->outlet_id,
                    'total' => $this->total,
                    'status' => $this->status,
                ]);
            } else {
                $pr = PurchaseRequisition::create([
                    'tanggal' => $this->tanggal,
                    'perihal' => $this->perihal,
                    'alasan' => $this->alasan,
                    'outlet_id' => $this->outlet_id,
                    'total' => $this->total,
                    'status' => $this->status,
                    'created_by' => Auth::id(),
                ]);
            }

            // Delete existing items if edit
            if ($this->isEdit) {
                $pr->items()->delete();
            }

            // Save items
            foreach ($this->items as $index => $item) {
                PrItem::create([
                    'purchase_requisition_id' => $pr->id,
                    'order' => $index + 1,
                    'nama_item' => $item['nama_item'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($pr)
                ->log($this->isEdit ? 'PR updated' : 'PR created');

            session()->flash('success', $this->status === 'submitted' 
                ? 'PR berhasil disubmit untuk approval' 
                : 'PR berhasil disimpan sebagai draft');

            return redirect()->route('pr.show', $pr->id);
        });
    }

    public function render()
    {
        return view('livewire.pr-form')->layout('components.layouts.app');
    }
}