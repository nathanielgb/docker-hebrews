<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuAddOn;
use App\Models\Menu;
use App\Models\Branch;
use App\Models\BranchMenuInventory;
use App\Models\InventoryCategory;

class MenuAddOnController extends Controller
{
    public function index($menu)
    {
        $menu = Menu::where('id', $menu)->first();

        if ($menu) {
            $addons = MenuAddOn::where('menu_id', $menu->id);
            $inventory_items = BranchMenuInventory::with('category')->where('branch_id', $menu->branch_id)->where('stock', '>', 0)->get();
            $addons = $addons->orderBy('menu_id')->paginate(20);

            return view('menu.add_ons', compact(
                'menu',
                'addons',
                'inventory_items'
            ));

        }
        return redirect()->back()->with('error', 'Menu does not exist.');
    }

    public function store(Request $request, $menu)
    {
        $menu = Menu::where('id', $menu)->first();

        if ($menu) {
            $request->validate([
                'isdinein' => 'required',
                'qty' => 'required|numeric|min:1|max:99999999',
                'inventory' => 'required|exists:branch_menu_inventories,id',
            ]);

            $exist = MenuAddOn::where('menu_id', $menu->id)
                ->where('inventory_id', $request->inventory)
                ->where('is_dinein', isset($request->isdinein) && $request->isdinein == 1 ? true : false)
                ->first();

            if ($exist) {
                return redirect()->back()->with('warning', 'Selected inventory already exist.');
            }

            $addons = MenuAddOn::create([
                'menu_id' => $menu->id,
                'inventory_id' => $request->inventory,
                'qty' => $request->qty,
                'is_dinein' => isset($request->isdinein) && $request->isdinein == 1 ? true : false,
            ]);

            return back()->with('success', 'Menu add-on added successfully.');
        }
        return redirect()->back()->with('error', 'Menu does not exist.');
    }

    //
    public function destroy (Request $request)
    {
        $addon = MenuAddOn::where('id', $request->id)->first();

        if ($addon) {
            $addon->delete();

            return back()->with('success', 'Menu add-on has been successfully removed.');
        }

        return redirect()->back()->with('error', 'Menu add-on does not exist.');
    }
}
