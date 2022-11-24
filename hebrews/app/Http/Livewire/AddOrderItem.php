<?php

namespace App\Http\Livewire;

use App\Models\Menu;
use Livewire\Component;
use App\Models\MenuAddOn;

class AddOrderItem extends Component
{
    public $menus;
    public $menuid;
    public $menuitem;
    public $orderItemAddons = [];
    public $addons;
    public $order;


    // Not user if used
    public function updatedMenuId ($id)
    {
        $this->menuitem = Menu::where('id', $id)->first();
        $this->orderItemAddons =[];
    }

    public function mount ()
    {
        $branch_id = $this->order->branch_id;

        $this->menus = Menu::whereHas('inventory', function ($q) use ($branch_id) {
            // Check branch of order
            $q->where('branch_id', $branch_id);
        })->get();

        $this->addons = MenuAddOn::whereHas('inventory', function ($q) use ($branch_id) {
            // Check branch of order
            $q->where('branch_id', $branch_id);

        })->get();

    }

    public function addAddon ()
    {
        $this->orderItemAddons[] = [
            'addon_id' => '',
            'qty' => 1
        ];
    }

    public function removeAddon ($index)
    {
        unset($this->orderItemAddons[$index]);
        $this->orderItemAddons = array_values($this->orderItemAddons);
    }

    public function render()
    {

        return view('livewire.add-order-item');
    }
}
