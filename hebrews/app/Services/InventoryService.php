<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\MenuAddOn;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMenuInventory;


class InventoryService
{
    public $items;

    public function __construct() {
        $this->items = collect();
    }

    public function setItem($item)
    {
        $this->items->add($item);

    }

    public function getItems()
    {
        return $this->items;
    }

    public function getInventoriesUsed()
    {
        $items = $this->items;
        $used_items = [];

        // Validate addon of array
        if (count($items) > 0) {
            $inventory_ids = array_unique($items->pluck('inventory_id')->toArray());

            $ivt = BranchMenuInventory::whereIn('id', $inventory_ids)->get();

            foreach($inventory_ids as $id) {
                $temp_items = $items->where('inventory_id', $id);
                $overall_stocks = $temp_items->sum('total_stocks');

                $ivt1 = $ivt->where('id', $id)->first();

                $used_items[$id] = [
                    'inventory_code' => $ivt1->inventory_code,
                    'name' => $ivt1->name,
                    'running_stock' => $ivt1->stock,
                    'total_used' => $overall_stocks
                ];
            }
        }

        return $used_items;
    }

    public function invalidCartItems()
    {
        $items = $this->items;
        $invalid_cartitems = [];

        // Validate addon of array
        if (count($items) > 0) {
            $inventory_ids = array_unique($items->pluck('inventory_id')->toArray());

            $ivt = BranchMenuInventory::whereIn('id', $inventory_ids)->get();

            foreach($inventory_ids as $id) {
                $temp_items = $items->where('inventory_id', $id);
                $overall_stocks = $temp_items->sum('total_stocks');

                $ivt1 = $ivt->where('id', $id)->first();

                if ($ivt1->stock < $overall_stocks) {
                    $cart_ids = array_unique($temp_items->pluck('cart_id')->toArray());
                    foreach ($cart_ids as $cid) {
                        $invalid_cartitems[] = $cid;
                    }
                }
            }
        }

        return $invalid_cartitems;
    }
}
