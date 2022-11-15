<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use App\Models\MenuInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Branch;
use App\Models\BranchMenuInventory;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menu = Menu::with('category');
        $inventory_items = MenuInventory::all()->toArray();

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
            'inventory_items',
            'categories',
        ));
    }
    public function store(StoreMenuRequest $request)
    {
        $inventory = MenuInventory::where('id', $request->inventory)->first();

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

    public function viewInventory(Request $request)
    {
        $inventory_items = new MenuInventory();

        if ($request->except(['page'])) {
            $inventory_items=$inventory_items->where(function ($query) use ($request) {
                if ($request->inventory_id !== null) {
                    $query->where('id', $request->inventory_id);
                }
                if ($request->name !== null) {
                    $query->where('name', 'LIKE', '%' . $request->name . '%');
                }
            });
        }

        $inventory_items = $inventory_items->orderBy('name')->paginate(20);
        $branches = Branch::all()->toArray();

        return view('menu.inventory', compact(
            'inventory_items',
            'branches'
        ));
    }


    public function addInventory(StoreInventoryRequest $request)
    {
        if (fmod($request->stock, 1) != 0.0 && $request->unit == 'pcs') {
            return redirect()->route('menu.view_inventory')->with('error', "Item $request->name cannot have a decimal stock.");
        }

        $inventory_code = strtolower(str_replace(' ', '', $request->inventory_code));

        // Check if inventory code exist
        $inventory = MenuInventory::where('inventory_code', $inventory_code)->first();
        if ($inventory) {
            return back()->with('error', "Failed to add inventory item. Inventory code is already used.");
        }

        MenuInventory::create([
            'inventory_code' => $inventory_code,
            'name' => $request->name,
            'unit' => $request->unit,
            'stock' => $request->stock,
            'previous_stock' => 0,
            'modified_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Item $request->name has been successfully added.");
    }

    public function updateInventory(Request $request)
    {
        $inventory_item = MenuInventory::where('id', $request->inventory_id)->first();

        if ($inventory_item) {
            $request->validate([
                'new_stock' => 'nullable|numeric|min:0',
            ]);

            if (fmod($request->new_stock, 1) != 0.0 && $inventory_item->unit == 'pcs') {
                return redirect()->route('menu.view_inventory')->with('error', "Item $inventory_item->name cannot have a decimal stock.");
            }

            // $increment_qty = $request->increment_qty ?? 0;
            $current_stock = $inventory_item->stock;
            // $updated_stock = $current_stock + $increment_qty;

            $inventory_item->update([
                'stock' => $request->new_stock,
                'previous_stock' => $current_stock,
                'modified_by' => auth()->user()->name
            ]);

            return redirect()->route('menu.view_inventory')->with('success', "Item $inventory_item->name has been updated successfully.");
        }
        return redirect()->route('menu.view_inventory')->with('error', 'Item does not exist.');
    }


    public function transferInventory(Request $request)
    {
        $inventory_item = MenuInventory::where('id', $request->inventory_id)->first();

        if ($inventory_item) {
            $request->validate([
                'transfer_stock' => 'required|numeric|min:1',
                'transfer_branch' => 'required',
            ]);

            if ($request->transfer_branch == 'dispose') {
                $current_stock = $inventory_item->stock;
                $dispose_stock = $request->transfer_stock;
                $updated_stock = $current_stock - $dispose_stock;

                if ($updated_stock < 0) {
                    $updated_stock = 0;
                }

                $inventory_item->update([
                    'stock' => $updated_stock,
                    'previous_stock' => $current_stock,
                    'modified_by' => auth()->user()->name
                ]);

                return redirect()->route('menu.view_inventory')->with('success', "Item $inventory_item->name stock disposed successfully.");
            } else {
                // Check if branch exist
                $branch = Branch::where('id', $request->transfer_branch)->first();
                if (!$branch) {
                    return redirect()->route('menu.view_inventory')->with('error', 'Failed to tranfer inventory item. Branch does not exist.');
                }

                // Check if transfer stock is greater than current stock
                if ($inventory_item->stock < $request->transfer_stock) {
                    return redirect()->route('menu.view_inventory')->with('error', 'Failed to tranfer inventory item. Transfer stock is invalid.');
                }

                // Transfer stock to new branch - Check if the item is in the branch and update otherwise create a new inventory item for that branch
                $branch_item = BranchMenuInventory::where('branch_id', $request->transfer_branch)
                    ->where('inventory_code', $inventory_item->inventory_code)
                    ->first();

                if ($branch_item) {
                    $current_branch_stock = $branch_item->stock;
                    $updated_branch_stock = $current_branch_stock + $request->transfer_stock;

                    $branch_item->update([
                        'stock' => $updated_branch_stock,
                        'previous_stock' => $current_branch_stock,
                        'modified_by' => auth()->user()->name
                    ]);
                } else {
                    BranchMenuInventory::create([
                        'name' => $inventory_item->name,
                        'inventory_code' => $inventory_item->inventory_code,
                        'unit' => $inventory_item->unit,
                        'stock' => $request->transfer_stock,
                        'previous_stock' => 0,
                        'branch_id' => $request->transfer_branch,
                        'modified_by' => auth()->user()->name
                    ]);
                }
                $current_stock = $inventory_item->stock;
                $updated_stock = $current_stock - $request->transfer_stock;

                // Deduct the transferred stock to the current inventory
                $inventory_item->update([
                    'stock' => $updated_stock,
                    'previous_stock' => $current_stock,
                    'modified_by' => auth()->user()->name
                ]);
            }

            return redirect()->route('menu.view_inventory')->with('success', "$request->transfer_stock $inventory_item->name items has been transfered successfully.");
        }
        return redirect()->route('menu.view_inventory')->with('error', 'Item does not exist.');
    }


    public function deleteInventory(Request $request)
    {
        $inventory_item = MenuInventory::where('id', $request->id)->first();
        if ($inventory_item) {
            // Prevent deletion if there is still linked products
            if (count($inventory_item->products) >= 1) {
                return redirect()->route('menu.view_inventory')->with('error', 'You cannot delete an inventory item that has a product linked to it.');
            }

            if (count($inventory_item->addons) >= 1) {
                return redirect()->route('menu.view_inventory')->with('error', 'You cannot delete an inventory item that has a add-on product linked to it.');
            }


            MenuInventory::where('id', $request->id)->delete();
            return redirect()->route('menu.view_inventory')->with('success', 'Item has been removed successfully.');
        }
        return redirect()->route('menu.view_inventory')->with('error', 'Item does not exist.');
    }
}
