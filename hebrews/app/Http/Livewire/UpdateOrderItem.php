<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateOrderItem extends Component
{
    public $order;
    public $orderItem;
    public $orderItemAddons = [];
    public $addons;
    protected $listeners = ['setItem'];

    public function setItem($orderItem)
    {
        $this->orderItem = $orderItem;
        $orderItem = json_decode($orderItem);
        $this->orderItemAddons = $orderItem->data;
    }

    public function mount ()
    {
        $branch_id = $this->order->branch_id;

        $this->addons = MenuAddOn::with('inventory')->whereHas('inventory', function ($q) use ($branch_id) {
            // Check branch of order
            $q->where('branch_id', $branch_id);
        })->get();

        $this->orderItemAddons =[];
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
        return view('livewire.update-order-item');
    }
}
