<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UpdateCart extends Component
{
    public $cart;
    public $cartItem = null;
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
        $cart = json_decode($cart);
    }

    public function mount ()
    {
    }

    public function render()
    {
        return view('livewire.update-cart');
    }
    
}
