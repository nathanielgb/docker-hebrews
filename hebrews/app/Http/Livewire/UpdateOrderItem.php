<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateOrderItem extends Component
{
    public $order;
    public $orderItem;
    public $addOns = [];
    public $selectedDineIn = 1;
    public $applyAddon = 1;
    public $orderQty;
    protected $listeners = ['setItem'];

    public function setItem($orderItem)
    {
        $this->orderItem = $orderItem;
        $orderItem = json_decode($orderItem, true);

        $this->orderQty = isset($orderItem['qty']) ? $orderItem['qty'] : 0;
        if (isset($orderItem['data']['is_dinein'])) {
            if ($orderItem['data']['is_dinein'] == 1) {
                $this->selectedDineIn = 1;
            } else {
                $this->selectedDineIn = 0;
            }
        } else {
            $this->selectedDineIn = 1;
        }

        if (isset($orderItem['data']['has_addons'])) {
            if ($orderItem['data']['has_addons'] == 1) {
                $this->applyAddon = 1;
            } else {
                $this->applyAddon = 0;
            }
        } else {
            $this->applyAddon = 0;
        }
        $this->addOns = MenuAddOn::where('menu_id', $orderItem['menu_id'])->where('is_dinein', $this->selectedDineIn)->get();
    }

    public function updatedSelectedDineIn($value)
    {
        $orderItem = json_decode($this->orderItem, true);
        $this->addOns = MenuAddOn::where('menu_id', $orderItem['menu_id'])->where('is_dinein', $value)->get();
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
        return view('livewire.update-order-item');
    }
}
