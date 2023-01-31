<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\BranchMenuInventory;

class CartService
{
    public $addons;

    public function add(Cart $cart)
    {
        // dd($cart);

        // if (isset($cart->data['has_addons']) && $cart->data['has_addons'] == 1) {
        //     $is_dinein = isset($cart->data['is_dinein']) && $cart->data['is_dinein'] == 1 ? true : false;
        //     $_addons = $cart->menu->getAddonItems($is_dinein);

        //     if (count($_addons) > 0) {
        //         $cart_qty = $cart->qty ?? 1;
        //         $cart_id = $cart->id;

        //        $_addons->each(function ($addon) use ($cart_qty) {


        //             $addon->total_stocks = $addon->qty * $cart_qty;

        //         });

        //     }
        // }

        // $inventory = BranchMenuInventory::where('id', $inventory_id)->first();

        // if ($inventory) {
        //     dd($inventory);


        // }
    }
}
