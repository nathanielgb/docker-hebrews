<?php
namespace App\Services;

use App\Models\MenuAddOn;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\DB;


class AddonService
{
    /**
     * Check if add-ons exist or inventory is enough to cover the quantity
     *
     * @param Model $productitem Menu item model
     * @param Boolean $is_dinein dine-in or takeout
     * @param Int $order_qty quantity of order
     * @return array
     */
    public function validateAddon($productitem, $is_dinein, $order_qty)
    {
        // Check if there is add ons
        $addons = MenuAddOn::where('menu_id', $productitem->id)->where('is_dinein', $is_dinein)->with('inventory')->get();

        if (count($addons) > 0) {

            foreach ($addons as $addon) {
                if (!$addon->inventory) {
                    return [
                        'status' => 'fail',
                        'message' => "An Add-on item is invalid."
                    ];
                    break;
                }

                $total_qty = $order_qty * intval($addon->qty);

                if ($addon->inventory->stock < $total_qty) {
                    return [
                        'status' => 'fail',
                        'message' => "An Add-on item (name: {$addon->inventory->name}) does not have enough stock."
                    ];
                    break;
                }
            }

            return [
                'status' => 'success'
            ];
        }
    }
}
