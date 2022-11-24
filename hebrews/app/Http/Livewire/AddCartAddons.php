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
        $addons = MenuAddOn::whereHas('inventory', function ($q) {
            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', auth()->user()->branch_id);
            }
        })->get();

        $this->addons = $addons;
        $this->cartAddons =[];
    }

    public function addAddon ()
    {
        $this->cartAddons[] = [
            'addon_id' => '',
            'name' => '',
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
