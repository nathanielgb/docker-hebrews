<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AddCart extends Component
{
    public $cart;
    public $cartItem = null;
    protected $listeners = ['setCartItem'];

    public function setCartItem($cart)
    {
        $this->cart = $cart;
    }

    public function mount ()
    {

    }

    public function render()
    {
        return view('livewire.add-cart');
    }
    
}
