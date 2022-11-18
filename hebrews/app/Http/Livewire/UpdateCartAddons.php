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

        $setAddons = $cart->data ?? [];
        $available_addons = [];

        foreach ($setAddons as $addon) {
            $validateAddOn = MenuAddOn::where('id', $addon->addon_id)->whereHas('inventory', function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            })->count();

            if ($validateAddOn > 0) {
                $available_addons[] = $addon;
            } else {
                $available_addons[] = (object) [
                    'addon_id' => $addon->addon_id,
                    'name' => "$addon->name (invalid)",
                    'qty' => $addon->qty,
                    'invalid' => 1
                ];
                $this->addons[] = [
                    'id' => $addon->addon_id,
                    'name' => "$addon->name (invalid)",
                    'qty' => $addon->qty,
                    'invalid' => 1
                ];
            }
        }

        $this->cartAddons = $available_addons;
    }

    public function mount ()
    {
        $addons = MenuAddOn::with('inventory')->whereHas('inventory', function ($q) {
            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', auth()->user()->branch_id);
            }
        })->get();

        foreach($addons as $addon) {
            $this->addons[] = [
                'id' => $addon->id,
                'name' => $addon->name,
                'inventory' => [
                    'id' => $addon->inventory->id,
                    'name' => $addon->inventory->name,
                    'unit' => $addon->inventory->unit,
                    'stock' => $addon->inventory->stock,
                    'inventory_code' => $addon->inventory->inventory_code,
                    'branch' => $addon->inventory->branch
                ]
            ];
        }

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
        return view('livewire.update-cart-addons');
    }
}
