<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\MenuInventory;
use App\Models\InventoryCategory;
use App\Imports\InventoryImport;
use App\Models\BranchMenuInventory;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreInventoryRequest;
use Illuminate\Validation\ValidationException;
use App\Models\ErrorLog;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory_items = new MenuInventory;

        if ($request->except(['page'])) {
            $inventory_items=$inventory_items->where(function ($query) use ($request) {
                if ($request->inventory_code !== null) {
                    $inventory_code = strtolower($request->inventory_code);
                    $query->whereRaw('inventory_code LIKE ?', ["%$inventory_code%"]);
                }
                if ($request->name !== null) {
                    $name = strtolower($request->name);
                    $query->whereRaw('name LIKE ?', ["%$name%"]);
                }
                if ($request->category !== null) {
                    $query->where('category_id', $request->category);
                }
            });
        }

        $inventory_items = $inventory_items->with('category')->orderBy('category_id', 'asc')->orderBy('name')->paginate(20);
        $branches = Branch::all()->toArray();
        $categories = InventoryCategory::orderBy('name', 'asc')->get();

        return view('menu.inventory', compact(
            'inventory_items',
            'branches',
            'categories'
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
            'category_id' => $request->category,
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
                return redirect()->back()->with('error', "Item $inventory_item->name cannot have a decimal stock.");
            }

            // $increment_qty = $request->increment_qty ?? 0;
            $current_stock = $inventory_item->stock;
            // $updated_stock = $current_stock + $increment_qty;

            $inventory_item->update([
                'stock' => $request->new_stock,
                'previous_stock' => $current_stock,
                'modified_by' => auth()->user()->name
            ]);

            return redirect()->back()->with('success', "Item $inventory_item->name has been updated successfully.");
        }
        return redirect()->back()->with('error', 'Item does not exist.');
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
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Invalid transfer stock.');
                }

                $inventory_item->update([
                    'stock' => $updated_stock,
                    'previous_stock' => $current_stock,
                    'modified_by' => auth()->user()->name
                ]);

                return redirect()->back()->with('success', "Item $inventory_item->name stock disposed successfully.");
            } else {
                // Check if branch exist
                $branch = Branch::where('id', $request->transfer_branch)->first();
                if (!$branch) {
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Branch does not exist.');
                }

                // Check if transfer stock is greater than current stock
                if ($request->transfer_stock > $inventory_item->stock) {
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Invalid transfer stock.');
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

            return redirect()->back()->with('success', "$request->transfer_stock $inventory_item->name items has been transfered  $branch->name to  successfully.");
        }
        return redirect()->back()->with('error', 'Item does not exist.');
    }


    public function deleteInventory(Request $request)
    {
        $inventory_item = MenuInventory::where('id', $request->id)->first();
        if ($inventory_item) {
            // Prevent deletion if there is still linked products
            // if (count($inventory_item->products) >= 1) {
            //     return redirect()->back()->with('error', 'You cannot delete an inventory item that has a product linked to it.');
            // }

            // if (count($inventory_item->addons) >= 1) {
            //     return redirect()->back()->with('error', 'You cannot delete an inventory item that has a add-on product linked to it.');
            // }


            MenuInventory::where('id', $request->id)->delete();
            return redirect()->back()->with('success', 'Item has been removed successfully.');
        }
        return redirect()->back()->with('error', 'Item does not exist.');
    }

    public function viewBranchInventory(Request $request)
    {
        if (auth()->user()->branch_id) {
            $inventory_items = BranchMenuInventory::where('branch_id', auth()->user()->branch_id);
            // $branches = Branch::where('id', auth()->user()->branch_id)->get();
            $branches = Branch::all();
        } else {
            $inventory_items = new BranchMenuInventory;
            $branches = Branch::all();
        }

        if ($request->except(['page'])) {
            $inventory_items=$inventory_items->where(function ($query) use ($request) {
                if ($request->inventory_code !== null) {
                    $inventory_code = strtolower($request->inventory_code);
                    $query->whereRaw('inventory_code LIKE ?', ["%$inventory_code%"]);
                }
                if ($request->name !== null) {
                    $name = strtolower($request->name);
                    $query->whereRaw('name LIKE ?', ["%$name%"]);
                }

                if ($request->branch_id !== null) {
                    $query->where('branch_id', $request->branch_id);
                }

                if ($request->category !== null) {
                    $query->where('category_id', $request->category);
                }
            });
        }

        $categories = InventoryCategory::orderBy('name', 'asc')->get();
        $inventory_items = $inventory_items->with('category', 'products', 'addons', 'branch')->orderBy('category_id', 'asc')->orderBy('name')->paginate(20);

        return view('menu.branches.inventory', compact(
            'inventory_items',
            'branches',
            'categories'
        ));
    }

    public function addBranchInventory(StoreInventoryRequest $request)
    {
        // Check if branch exist
        $branch = Branch::where('id', $request->branch_id)->first();
        if (!$branch) {
            return redirect()->back()->with('error', 'Failed to add inventory item. Branch does not exist.');
        }

        $inventory_code = strtolower(str_replace(' ', '', $request->inventory_code));

        // Check if inventory code exist
        $inventory = BranchMenuInventory::where('inventory_code', $inventory_code)->where('branch_id', $request->branch_id)->first();
        if ($inventory) {
            return back()->with('error', "Failed to add inventory item. Inventory code is already used.");
        }

        if (fmod($request->stock, 1) != 0.0 && $request->unit == 'pcs') {
            return redirect()->back()->with('error', "Item $request->name cannot have a decimal stock.");
        }

        BranchMenuInventory::create([
            'category_id' => $request->category,
            'inventory_code' => $inventory_code,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'stock' => $request->stock,
            'previous_stock' => 0,
            'modified_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Item $request->name has been successfully added.");
    }
    public function updateBranchInventory(Request $request)
    {
        $inventory_item = BranchMenuInventory::where('id', $request->inventory_id)->first();

        if ($inventory_item) {
            $request->validate([
                'new_stock' => 'nullable|numeric|min:0',
            ]);

            if (fmod($request->new_stock, 1) != 0.0 && $inventory_item->unit == 'pcs') {
                return redirect()->back()->with('error', "Item $inventory_item->name cannot have a decimal stock.");
            }

            $current_stock = $inventory_item->stock;

            $inventory_item->update([
                'stock' => $request->new_stock,
                'previous_stock' => $current_stock,
                'modified_by' => auth()->user()->name
            ]);

            return redirect()->back()->with('success', "Item $inventory_item->name has been updated successfully.");
        }
        return redirect()->back()->with('error', 'Item does not exist.');
    }

    public function deleteBranchInventory(Request $request)
    {
        $inventory_item = BranchMenuInventory::where('id', $request->id)->first();
        if ($inventory_item) {
            // Prevent deletion if there is still linked products
            if (count($inventory_item->products) >= 1) {
                return redirect()->back()->with('error', 'You cannot delete an inventory item that has a product linked to it.');
            }

            if (count($inventory_item->addons) >= 1) {
                return redirect()->back()->with('error', 'You cannot delete an inventory item that has a add-on product linked to it.');
            }


            BranchMenuInventory::where('id', $request->id)->delete();
            return redirect()->back()->with('success', 'Item has been removed successfully.');
        }
        return redirect()->back()->with('error', 'Item does not exist.');
    }


    public function transferBranchInventory(Request $request)
    {
        $inventory_item = BranchMenuInventory::where('id', $request->inventory_id)->first();

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
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Invalid transfer stock.');
                }

                $inventory_item->update([
                    'stock' => $updated_stock,
                    'previous_stock' => $current_stock,
                    'modified_by' => auth()->user()->name
                ]);

                return redirect()->back()->with('success', "Item $inventory_item->name stock disposed successfully.");
            }


            // Check if transfer stock is greater than current stock
            if ($inventory_item->stock < $request->transfer_stock) {
                return redirect()->back()->with('error', 'Failed to tranfer inventory item. Invalid transfer stock.');
            }

            if ($request->transfer_branch == 'warehouse') {
                $branch_name = 'Warehouse';

                // Transfer stock to main branch - Check if the item is in the branch and update otherwise create a new inventory item for that branch
                $branch_item = MenuInventory::where('inventory_code', $inventory_item->inventory_code)->first();

                if ($branch_item) {
                    $current_branch_stock = $branch_item->stock;
                    $updated_branch_stock = $current_branch_stock + $request->transfer_stock;

                    $branch_item->update([
                        'stock' => $updated_branch_stock,
                        'previous_stock' => $current_branch_stock,
                        'modified_by' => auth()->user()->name
                    ]);
                } else {
                    MenuInventory::create([
                        'name' => $inventory_item->name,
                        'inventory_code' => $inventory_item->inventory_code,
                        'unit' => $inventory_item->unit,
                        'stock' => $request->transfer_stock,
                        'previous_stock' => 0,
                        'modified_by' => auth()->user()->name
                    ]);
                }
            } else {
                // Check if branch exist
                $branch = Branch::where('id', $request->transfer_branch)->first();
                if (!$branch) {
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Branch does not exist.');
                }

                if ($request->transfer_branch == $inventory_item->branch_id) {
                    return redirect()->back()->with('error', 'Failed to tranfer inventory item. Cannot transfer to own branch.');
                }

                $branch_name = $branch->name;

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
            }

            $current_stock = $inventory_item->stock;
            $updated_stock = $current_stock - $request->transfer_stock;

            // Deduct the transferred stock to the current inventory
            $inventory_item->update([
                'stock' => $updated_stock,
                'previous_stock' => $current_stock,
                'modified_by' => auth()->user()->name
            ]);

            return redirect()->back()->with('success', "$request->transfer_stock $inventory_item->name items has been transfered to $branch_name  successfully.");
        }
        return redirect()->back()->with('error', 'Item does not exist.');
    }

    public function viewImportInventory ()
    {
        return view('menu.inventory.import');
    }

    public function importInventory (Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);

        $file = $request->file('file');
        $records = [];

        try {
            $import = new InventoryImport;
            $import->import($file);

            $records = $import->records;

        } catch (ValidationException $e) {
            ErrorLog::create([
                'location' => 'InventoryController.importInventory',
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
}
