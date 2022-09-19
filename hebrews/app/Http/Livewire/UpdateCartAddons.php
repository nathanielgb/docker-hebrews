<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use Livewire\Component;
use App\Models\MenuAddOn;

class UpdateCartAddons extends Component
{
    public $cart;
    public $cartItem = null;
    public $cartAddons = [];
    public $addons = [];
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
        $cart = json_decode($cart);
        $this->cartAddons = $cart->data;
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
        return view('livewire.update-cart-addons');
    }
}
