<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\MenuInventory;
use App\Imports\InventoryImport;
use App\Models\BranchMenuInventory;
use App\Models\InventoryCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreInventoryRequest;
use Illuminate\Validation\ValidationException;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = new InventoryCategory;

        if ($request->except(['page'])) {
            $categories=$categories->where(function ($query) use ($request) {

            });
        }

        $categories = $categories->orderBy('name')->paginate(20);

        return view('menu.inventory.category.index', compact(
            'categories'
        ));
    }

    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3', 'max:50', Rule::unique('inventory_categories')->where('name', $request->input('name'))]
        ]);

        $category = InventoryCategory::create([
            'name' => $request->name
        ]);

        return back()->with('success', "Item $request->name has been successfully added.");
    }

    public function deleteCategory(Request $request)
    {
        $category = InventoryCategory::where('id', $request->id)->first();

        if ($category) {
            // Prevent deletion if there is still linked products
            if (count($category->branchInventories) >= 1) {
                return redirect()->back()->with('error', 'You cannot delete category that has an item linked to it.');
            }

            // Prevent deletion if there is still linked products
            if (count($category->wareHouseInventories) >= 1) {
                return redirect()->back()->with('error', 'You cannot delete category that has an item linked to it.');
            }


            // Delete all menu item with the same category
            // $deleted_menu_items = DB::table('menus')->where('category_id', '=', $category->id)->delete();
            $deleted_category = DB::table('inventory_categories')->where('id', '=', $category->id)->delete();

            return back()->with('success', 'Category has been successfully removed.');
        }

        return back()->with('error', 'Category does not exist or has been already removed.');
    }
}
