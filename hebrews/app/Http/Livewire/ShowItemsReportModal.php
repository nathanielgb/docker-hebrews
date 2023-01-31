<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowItemsReportModal extends Component
{
    public $details = [];
    public $field;
    protected $listeners = ['setItem'];

    public function setItem($field, $details)
    {
        $this->field = $field;
        $this->details = !empty($details) ? json_decode($details) : [];
    }


    public function render()
    {
        return view('livewire.show-items-report-modal');
    }
}
