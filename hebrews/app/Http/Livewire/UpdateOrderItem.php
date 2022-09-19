<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateOrderItem extends Component
{
    public $order;
    public $orderItemAddons = [];
    public $addons;
    protected $listeners = ['setItem'];

    public function setItem($order)
    {
        $this->order = $order;
        $order = json_decode($order);
        $this->orderItemAddons = $order->data;
    }

    public function mount ()
    {
        $this->addons = MenuAddOn::all();
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
