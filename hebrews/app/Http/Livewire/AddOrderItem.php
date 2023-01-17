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
    public $order;


    // Not user if used
    public function updatedMenuId ($id)
    {
        $this->menuitem = Menu::where('id', $id)->first();
    }

    public function mount ()
    {
        $branch_id = $this->order->branch_id;

        $this->menus = Menu::whereHas('inventory', function ($q) use ($branch_id) {
            // Check branch of order
            $q->where('branch_id', $branch_id);
            $q->where('stock', '>', 0);
        })->orderBy('name')->get();
    }



    public function render()
    {

        return view('livewire.add-order-item');
    }
}
