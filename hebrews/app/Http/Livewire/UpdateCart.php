<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateCart extends Component
{
    public $cart;
    public $cartItem = null;
    public $addOns = [];
    public $selectedDineIn = 1;
    public $applyAddon = 1;
    public $orderQty;
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
        $cart = json_decode($cart, true);
        $this->orderQty = isset($cart['qty']) ? $cart['qty'] : 0;

        if (isset($cart['data']['is_dinein'])) {
            if ($cart['data']['is_dinein'] == 1) {
                $this->selectedDineIn = 1;
            } else {
                $this->selectedDineIn = 0;
            }
        } else {
            $this->selectedDineIn = 1;
        }

        if (isset($cart['data']['has_addons'])) {
            if ($cart['data']['has_addons'] == 1) {
                $this->applyAddon = 1;
            } else {
                $this->applyAddon = 0;
            }
        } else {
            $this->applyAddon = 0;
        }


        $this->addOns = MenuAddOn::where('menu_id', $cart['menu_id'])->where('is_dinein', $this->selectedDineIn)->get();
    }

    public function updatedSelectedDineIn($value)
    {
        $_cart = json_decode($this->cart, true);
        $this->addOns = MenuAddOn::where('menu_id', $_cart['menu_id'])->where('is_dinein', $value)->get();
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
        return view('livewire.update-cart');
    }

}
