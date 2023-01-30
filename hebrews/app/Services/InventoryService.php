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

    public function getInventoriesUsedByOrder($items)
    {
        $oitems = $items;
        $used_items = [];
        $menu_ids = $oitems->pluck('menu_id');
        $_addons = MenuAddOn::whereIn('menu_id', $menu_ids)->get();

        // Validate addon of array
        if (count($oitems) > 0) {

            foreach($oitems as $oitem) {
                if ($oitem->inventory_id != null) {
                    $oitem->total_stocks = $oitem->units * $oitem->qty;
                    $this->setItem($oitem);
                }
                $is_dinein = isset($oitem->data['is_dinein']) && $oitem->data['is_dinein'] == 1 ? true : false;

                if (isset($oitem->data['has_addons']) && $oitem->data['has_addons'] == 1) {
                    $addons = $_addons->where('menu_id', $oitem->menu_id)->where('is_dinein', $is_dinein);

                    if (count($addons) > 0) {
                        $o_qty = $oitem->qty ?? 1;

                        $addons->map(function ($addon) use ($o_qty) {

                            $addon->total_stocks = $addon->qty * $o_qty;

                            $this->setItem($addon);
                        });
                    }
                }

            }

            $inventories_used = $this->items;

            if (count($inventories_used) > 0) {
                $inventory_ids = array_unique($inventories_used->pluck('inventory_id')->toArray());
                $ivt = BranchMenuInventory::whereIn('id', $inventory_ids)->get();
                foreach($inventory_ids as $id) {
                    $temp_items = $inventories_used->where('inventory_id', $id);
                    $overall_stocks = $temp_items->sum('total_stocks');

                    $ivt1 = $ivt->where('id', $id)->first();
                    $invalid = $ivt1->stock < $overall_stocks ? true : false;

                    $used_items[$id] = [
                        'inventory_id' => $ivt1->id,
                        'inventory_code' => $ivt1->inventory_code,
                        'name' => $ivt1->name,
                        'running_stock' => $ivt1->stock,
                        'total_used' => $overall_stocks,
                        'invalid' => $invalid
                    ];
                }
            }

        }

        return $used_items;
    }

    public function getConfirmedInventoriesUsedByOrder ($items)
    {
        $oitems = $items;
        $used_items = [];
        $menu_ids = $oitems->pluck('menu_id');

        // Validate addon of array
        if (count($oitems) > 0) {

            foreach($oitems as $oitem) {
                if ($oitem->inventory_id != null) {
                    $oitem->total_stocks = $oitem->units * $oitem->qty;
                    $this->setItem($oitem);
                }

                $addons = $oitem->addons;

                if (count($addons) > 0) {

                    foreach ($addons as $addon) {
                        $addon->total_stocks = $addon->qty;
                        $this->setItem($addon);
                    }
                }
            }
        }

        $inventories_used = $this->items;

        // dd($inventories_used);
        if (count($inventories_used) > 0) {
            $inventory_ids = array_unique($inventories_used->pluck('inventory_id')->toArray());

            foreach($inventory_ids as $id) {
                $temp_items = $inventories_used->where('inventory_id', $id);
                $overall_stocks = $temp_items->sum('total_stocks');
                $ivt1 = $temp_items->first();

                $used_items[$id] = [
                    'inventory_id' => $ivt1->id,
                    'inventory_code' => $ivt1->inventory_code,
                    'name' => $ivt1->inventory_name,
                    'total_used' => $overall_stocks,
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
