<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowAddons extends Component
{
    public $addons = [];
    protected $listeners = ['setAddOnItem'];

    public function setAddOnItem($addons)
    {
        $this->addons = json_decode($addons);
    }

    public function render()
    {
        return view('livewire.show-addons');
    }
}
