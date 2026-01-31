<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseRequisition;

class PdfDownloadInstructions extends Component
{
    public $prId;
    public $showModal = false;
    public $pr;

    public function mount($prId)
    {
        $this->prId = $prId;
        $this->pr = PurchaseRequisition::findOrFail($prId);
    }

    public function openInstructions()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function downloadDocx()
    {
        $this->closeModal();
        return redirect()->route('pr.docx', $this->prId);
    }

    public function openILovePdf()
    {
        $this->closeModal();
        // Buka iLovePDF di tab baru
        $this->dispatch('openILovePdf');
    }

    public function render()
    {
        return view('livewire.pdf-download-instructions');
    }
}