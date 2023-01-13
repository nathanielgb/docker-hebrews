<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateOrderItem extends Component
{
    public $order;
    public $orderItem;
    protected $listeners = ['setItem'];

    public function setItem($orderItem)
    {
        $this->orderItem = $orderItem;
        $orderItem = json_decode($orderItem);
    }

    public function mount ()
    {
        $branch_id = $this->order->branch_id;
    }


    public function render()
    {
        return view('livewire.update-order-item');
    }
}
