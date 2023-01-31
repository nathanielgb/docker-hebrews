<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Branch;
use App\Models\ErrorLog;
use App\Models\MenuAddOn;
use App\Imports\MenuImport;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\MenuInventory;
use App\Models\InventoryCategory;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMenuInventory;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $inventories = new BranchMenuInventory;

        if (auth()->user()->branch_id) {
            $menu = Menu::where(function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            });

            $inventories =$inventories->where('branch_id', auth()->user()->branch_id);
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $menu = Menu::with('category','inventory','branch', 'inventory.branch');
            $branches = Branch::all();
        }

        $inventory_items = $inventories->orderBy('name', 'asc')->get();

        if ($request->except(['page'])) {
            $menu=$menu->where(function ($query) use ($request) {
                if ($request->branch_id !== null) {
                    $query->where('branch_id',  $request->branch_id);
                }
                if ($request->menu_id !== null) {
                    $query->where('id',  $request->menu_id);
                }
                if ($request->code !== null) {
                    $query->where('code', 'LIKE', '%' . $request->code . '%');
                }
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
        if (isset($request->inventory)) {
            $inventory = BranchMenuInventory::where('id', $request->inventory)->first();

            if (!$inventory) {
                return back()->with('error', 'Item Inventory does not exist.');
            }

            // Check the minimum unit required
            if($inventory->unit == 'boxes' && $request->unit < 1) {
                return back()->with('error', "The box must be at least 1.");
            }

            if($inventory->unit == 'pcs' && $request->unit < 1) {
                return back()->with('error', "The unit must be at least 1.");
            }

            if ($inventory->unit != 'pcs' && $request->unit < 0.001) {
                return back()->with('error', "The unit must be at least 0.001.");
            }
        }

        $menu = Menu::create([
            'code' => $request->code,
            'name' => $request->menu,
            'units' => $request->unit,
            'reg_price' => $request->reg_price,
            'retail_price' => $request->retail_price,
            'wholesale_price' => $request->wholesale_price,
            'rebranding_price' => $request->rebranding_price,
            'distributor_price' => $request->distributor_price,
            'category_id' => $request->category,
            'inventory_id' => $request->inventory ?? null,
            'branch_id' => $request->branch ?? null,
            'sub_category' => $request->sub_category,
            'is_beans' => isset($request->is_beans) ? true : false
        ]);

        return back()->with('success', 'Successfully added ' . $request->menu . ' to the menu.');
    }

    public function viewImport ()
    {
        return view('menu.import');
    }

    public function import (Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);


        $file = $request->file('file');
        $records = [];

        try {
            $import = new MenuImport;
            $import->import($file);

            $records = $import->records;

        } catch (ValidationException $e) {
            ErrorLog::create([
                'location' => 'MenuController.import',
                'message' => gettype($e->errors()) == 'string' ? $e->errors() : json_encode($e->errors())
            ]);

            return redirect()
                ->back()
                ->with('error', "Failed to import file. Please try again.");
        }


        return redirect()
            ->back()
            ->with('success', "Records are successfully imported successfully.")
            ->with('records', $records);
    }

    public function update(UpdateMenuRequest $request)
    {
        $menu = Menu::where('id', $request->menu_id)->first();

        if ($menu) {

            if (isset($request->inventory)) {
                $inventory = BranchMenuInventory::where('id', $request->inventory)->first();

                if (!$inventory) {
                    return back()->with('error', "Failed to update Item $request->menu. Inventory does not exist.");
                }

                if (fmod($request->unit, 1) != 0.0 && $inventory->unit == 'pcs') {
                    return back()->with('error', "Item $request->menu cannot have a decimal stock.");
                }

                // Check the minimum unit required
                if($inventory->unit == 'boxes' && $request->unit < 1) {
                    return back()->with('error', "The box must be at least 1.");
                }

                if($inventory->unit == 'pcs' && $request->unit < 1) {
                    return back()->with('error', "The unit must be at least 1.");
                }

                if ($inventory->unit != 'pcs' && $request->unit < 0.001) {
                    return back()->with('error', "The unit must be at least 0.001.");
                }
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
                'is_beans' => isset($request->is_beans) ? true : false
            ]);
            return redirect()->route('menu.index')->with('success', 'Item ' . $menu->name . ' updated successfully.');
        }
        return redirect()->route('menu.index')->with('error', 'Menu item does not exist.');
    }

    public function delete(Request $request)
    {
        $menu = Menu::where('id', $request->id)->first();

        if ($menu) {
            $addons = MenuAddOn::where('menu_id', $menu->id)->delete();
            $menu->delete();
            // Delete Inventory item
            // MenuCategory::where('id', $menu->id)->delete();

            return redirect()->route('menu.index')->with('success', 'Menu item was deleted successfully.');
        }
        return redirect()->route('menu.index')->with('error', 'Menu item does not exist.');
    }

    public function viewCategories(Request $request)
    {
        $categories = MenuCategory::with('menus')->OrderBy('name')->paginate(20);

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
                'sub' => $request->sub,
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
            // $deleted_menu_items = DB::table('menus')->where('category_id', '=', $category->id)->delete();
            $deleted_category = DB::table('menu_categories')->where('id', '=', $category->id)->delete();

            return back()->with('success', 'Category has been successfully removed.');
        }

        return back()->with('error', 'Category does not exist or has been already removed.');
    }
}
