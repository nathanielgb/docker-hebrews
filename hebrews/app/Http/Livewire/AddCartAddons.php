<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MenuAddOn;

class AddCartAddons extends Component
{
    public $cart;
    public $cartItem = null;
    public $cartAddons = [];
    public $addons = [];
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
        $this->cartAddons = [];
    }

    public function mount ()
    {
        $this->addons = MenuAddOn::all();
        $this->cartAddons =[];
    }

    public function addAddon ()
    {
        $this->cartAddons[] = [
            'addon_id' => '',
            'qty' => 1
        ];
    }

    public function removeAddon ($index)
    {
        unset($this->cartAddons[$index]);
        $this->cartAddons = array_values($this->cartAddons);
    }

    public function render()
    {
        return view('livewire.add-cart-addons');
    }
}
