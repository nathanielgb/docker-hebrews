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

        $this->menus = Menu::where(function ($q1) use ($branch_id) {
            $q1->doesntHave('inventory');
            $q1->where('branch_id', $branch_id);

        })->orWhereHas('inventory', function ($q2) use ($branch_id) {
            $q2->where('stock', '>', 0);
            $q2->where('branch_id', $branch_id);
        })->orderBy('name')->get();
    }



    public function render()
    {

        return view('livewire.add-order-item');
    }
}
