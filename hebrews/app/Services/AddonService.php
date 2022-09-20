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
     * @param Array $addons attached add-ons to the cart item
     * @return array
     */
    public function validateAddon($addons)
    {
        // Check if there is add ons
        if ($addons) {
            $addOnData = $addons;
            $finalData = [];
            foreach ($addons as $index => $value) {
                if (!array_key_exists('addon_id', $value)) {
                    unset($addOnData[$index]);
                    $addOnData = array_values($addOnData);
                } else {
                    $addon = MenuAddOn::where('id', $value['addon_id'])->first();
                    if (!$addon) {
                        return [
                            'status' => 'fail',
                            'message' => 'An Add-on item you entered does not exist.'
                        ];
                        break;
                    }

                    // Check if quantity is negative or enough stock
                    if ($value['qty'] <= 0) {
                        return [
                            'status' => 'fail',
                            'message' => 'An Add-on item quantity is invalid.'
                        ];
                        break;
                    }
                    if ($addon->inventory->stock < $value['qty']) {
                        return [
                            'status' => 'fail',
                            'message' => 'Add-on (name: ' . $addon->name . ') does not have enough stock.'
                        ];
                        break;
                    }

                    $finalData[] = [
                        'addon_id' => $addon->id,
                        'name' => $addon->name,
                        'qty' => $value['qty']
                    ];
                }
            }

            // remove duplicates
            $record = array();
            $ids = array();
            foreach($finalData as $key=>$value){
                if (!in_array($value['addon_id'], $ids)) {
                    $ids[] = $value['addon_id'];
                    $record[$key] = $value;
                }
            }
            $record = array_values($record);

            return [
                'status' => 'success',
                'data' => $record,
                'ids' => $ids
            ];
        }
    }
}
