<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\MenuInventory;
use App\Models\MenuAddOn;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreInventoryRequest;
use Illuminate\Support\Facades\Redis;

class MenuAddOnController extends Controller
{
    public function index(Request $request)
    {
        $addons = MenuAddOn::orderBy('name');
        $inventory_items = MenuInventory::all();

        $addons = $addons->paginate(20);


        return view('menu.add_ons', compact(
            'addons',
            'inventory_items'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'inventory' => 'required|exists:menu_inventories,id',
        ]);

        $addons = MenuAddOn::create([
            'name' => $request->name,
            'inventory_id' => $request->inventory
        ]);

        return back()->with('success', 'Menu Add-on added successfully.');
    }
}
