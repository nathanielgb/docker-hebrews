<?php

namespace App\Http\Livewire;

use App\Models\MenuAddOn;
use Livewire\Component;

class AddCart extends Component
{
    public $cart;
    public $cartItem = null;
    public $addOns = [];
    public $selectedDineIn = 1;
    public $applyAddon = 1;
    public $orderQty = 1;
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
        $_cart = json_decode($cart, true);

        $this->addOns = MenuAddOn::where('menu_id', $_cart['id'])->where('is_dinein', $this->selectedDineIn)->get();
        $this->applyAddon = 1;
    }

    public function updatedSelectedDineIn($value)
    {
        $_cart = json_decode($this->cart, true);
        $this->addOns = MenuAddOn::where('menu_id', $_cart['id'])->where('is_dinein', $value)->get();
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
        return view('livewire.add-cart');
    }

}
