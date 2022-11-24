<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuAddOn;
use App\Models\Branch;
use App\Models\BranchMenuInventory;

class MenuAddOnController extends Controller
{
    public function index(Request $request)
    {
        $addons = MenuAddOn::whereHas('inventory', function ($q) {
            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', auth()->user()->branch_id);
            }
        });

        if (auth()->user()->branch_id) {
            $inventory_items = BranchMenuInventory::where('branch_id', auth()->user()->branch_id)->get();;
            $branches =  Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $inventory_items = BranchMenuInventory::all();
            $branches = Branch::all();
        }

        $addons = $addons->orderBy('name')->paginate(20);

        return view('menu.add_ons', compact(
            'addons',
            'branches',
            'inventory_items'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|alpha_dash',
            'inventory' => 'required|exists:branch_menu_inventories,id',
        ]);

        $addons = MenuAddOn::create([
            'name' => $request->name,
            'inventory_id' => $request->inventory
        ]);

        return back()->with('success', 'Menu add-on added successfully.');
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
