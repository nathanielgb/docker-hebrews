<?php

namespace App\Http\Livewire;

use App\Models\Menu;
use Livewire\Component;
use App\Models\MenuAddOn;

class AddOrderItem extends Component
{
    public $menuid;
    public $menuitem;
    public $addOns = [];
    public $order;
    public $selectedDineIn = 1;
    public $applyAddon = 1;
    public $orderQty = 1;

    // Not user if used
    public function updatedMenuId ($id)
    {
        $this->menuitem = Menu::where('id', $id)->first();

        $this->addOns = MenuAddOn::where('menu_id', $id)->where('is_dinein', $this->selectedDineIn)->with('inventory')->get();
        $this->applyAddon = 1;
    }

    public function updatedSelectedDineIn($value)
    {
        $menuitem = json_decode($this->menuitem, true);
        $this->addOns = MenuAddOn::where('menu_id', $menuitem['id'])->where('is_dinein', $value)->with('inventory')->get();
    }

    public function updatedOrderQty($value)
    {
        $this->orderQty = $value;
    }

    public function mount ()
    {
    }

    public function render()
    {

        return view('livewire.add-order-item');
    }
}
