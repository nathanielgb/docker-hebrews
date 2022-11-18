<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use App\Models\MenuInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Branch;
use App\Models\BranchMenuInventory;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->branch_id) {
            $menu = Menu::whereHas('inventory', function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            });

            $inventory_items = BranchMenuInventory::where('branch_id', auth()->user()->branch_id)->get();
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $menu = Menu::with('category');
            $inventory_items = BranchMenuInventory::all();
            $branches = Branch::all();
        }

        if ($request->except(['page'])) {
            $menu=$menu->where(function ($query) use ($request) {
                if ($request->menu !== null) {
                    $query->where('name', 'LIKE', '%' . $request->menu . '%');
                }
                if ($request->category !== null) {
                    $query->where('category_id', 'LIKE', '%' . $request->category . '%');
                }
            });
        }

        $menu = $menu->orderBy('name')->paginate(20);
        $categories = MenuCategory::orderBy('name')->get();
        return view('menu.index', compact(
            'menu',
            'categories',
            'inventory_items',
            'branches'
        ));
    }
    public function store(StoreMenuRequest $request)
    {
        $inventory = BranchMenuInventory::where('id', $request->inventory)->first();

        if ($inventory) {
            // Check the minimum unit required
            if($inventory->unit == 'boxes' && $request->unit < 1) {
                return back()->with('error', "The box must be at least 1.");
            }

            if($inventory->unit == 'pcs' && $request->unit < 1) {
                return back()->with('error', "The unit must be at least 1.");
            }

            if ($inventory->unit != 'pcs' && $request->unit < 0.01) {
                return back()->with('error', "The unit must be at least 0.01.");
            }

            $menu = Menu::create([
                'name' => $request->menu,
                'units' => $request->unit,
                'reg_price' => $request->reg_price,
                'retail_price' => $request->retail_price,
                'wholesale_price' => $request->wholesale_price,
                'rebranding_price' => $request->rebranding_price,
                'distributor_price' => $request->distributor_price,
                'category_id' => $request->category,
                'inventory_id' => $request->inventory,
                'sub_category' => $request->sub_category,
            ]);

            return back()->with('success', 'Successfully added ' . $request->menu . ' to the menu.');
        }
        return back()->with('error', 'Item Inventory does not exist.');
    }

    // public function viewUpdate(Request $request)
    // {
    //     $item = Menu::where('id', $request->menu_id)->first();

    //     if ($item) {
    //         $categories = MenuCategory::orderBy('name')->get();
    //         $inventory_items = MenuInventory::all();


    //         return view('menu.sections.update', compact(
    //             'item',
    //             'categories',
    //             'inventory_items'
    //         ));
    //     }
    // }

    public function update(UpdateMenuRequest $request)
    {
        $menu = Menu::where('id', $request->menu_id)->first();

        if ($menu) {

            if (!$menu->inventory) {
                return back()->with('error', "Failed to update Item $request->menu. Inventory does not exist.");
            }

            if (fmod($request->unit, 1) != 0.0 && $menu->inventory->unit == 'pcs') {
                return back()->with('error', "Item $request->menu cannot have a decimal stock.");
            }

            // Check the minimum unit required
            if($menu->inventory->unit == 'boxes' && $request->unit < 1) {
                return back()->with('error', "The box must be at least 1.");
            }

            if($menu->inventory->unit == 'pcs' && $request->unit < 1) {
                return back()->with('error', "The unit must be at least 1.");
            }

            if ($menu->inventory->unit != 'pcs' && $request->unit < 0.01) {
                return back()->with('error', "The unit must be at least 0.01.");
            }
            $menu->update([
                'name' => $request->menu,
                'units' => $request->unit,
                'reg_price' => $request->reg_price,
                'retail_price' => $request->retail_price,
                'wholesale_price' => $request->wholesale_price,
                'rebranding_price' => $request->rebranding_price,
                'distributor_price' => $request->distributor_price,
                'category_id' => $request->category,
                'inventory_id' => $request->inventory,
                'sub_category' => $request->sub_category,
            ]);
            return redirect()->route('menu.index')->with('success', 'Item ' . $menu->name . ' updated successfully.');
        }
        return redirect()->route('menu.index')->with('error', 'Menu item does not exist.');
    }

    public function delete(Request $request)
    {
        $menu = Menu::where('id', $request->id)->first();

        if ($menu) {
            $menu->delete();
            // Delete Inventory item
            // MenuCategory::where('id', $menu->id)->delete();

            return redirect()->route('menu.index')->with('success', 'Menu item was deleted successfully.');
        }
        return redirect()->route('menu.index')->with('error', 'Menu item does not exist.');
    }

    public function viewCategories(Request $request)
    {
        $categories = MenuCategory::OrderBy('name')->paginate(20);

        return view('menu.categories', compact(
            'categories',
        ));
    }

    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'from' => 'required|max:255',
        ]);

        MenuCategory::create([
            'name' => strtoupper($request->name),
            'from' => $request->from,
            'sub' => $request->subcat,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request)
    {
        $category = MenuCategory::where('id', $request->category_id)->first();

        if ($category) {
            // Prevent deletion if there is still linked products
            if (count($category->menus) >= 1) {
                return redirect()->back()->with('error', 'You cannot update category that has a menu item linked to it.');
            }

            $request->validate([
                'name' => 'required|max:255',
                'from' => 'required|max:255',
            ]);


            $category->update([
                'name' => strtoupper($request->name),
                'from' => $request->from,
                'sub' => $request->subcat,
            ]);

            return back()->with('success', "Category $category->name updated successfully.");
        }

        return back()->with('error', 'Failed to update category. Record does not exist.');
    }

    public function deleteCategory(Request $request)
    {
        $category = MenuCategory::where('id', $request->id)->first();

        if ($category) {
            // Prevent deletion if there is still linked products
            if (count($category->menus) >= 1) {
                return redirect()->back()->with('error', 'You cannot delete category that has a menu item linked to it.');
            }

            // Delete all menu item with the same category
            $deleted_menu_items = DB::table('menus')->where('category_id', '=', $category->id)->delete();
            $deleted_category = DB::table('menu_categories')->where('id', '=', $category->id)->delete();

            return back()->with('success', 'Category has been successfully removed.');
        }

        return back()->with('error', 'Category does not exist or has been already removed.');
    }
}
