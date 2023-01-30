<?php
namespace App\Services;

use App\Models\AddonOrderItem;
use App\Models\BranchMenuInventory;
use App\Models\ErrorLog;
use App\Models\Menu;
use App\Models\MenuAddOn;
use App\Models\MenuInventory;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLog;


class OrderService
{

    /**
     *Add new item to the order
     *
     * @param Object $order model of current order
     * @param String $id menu id of the item being added
     * @param String $quantity order quantity of the item
     * @param String $type product type of menu item
     * @param String $grind_type grind type of menu item (if beans)
     * @param Boolean $isdinein if dine-in or take-out order
     * @param Boolean $hasAddons has addons
     * @return array|string
     */
    public function addItem($order, $id, $qty, $type, $grind_type, $isdinein,  $hasAddons)
    {
        $item = Menu::where('id', $id)->first();

        // Check if item exist
        if (!$item) {
            throw new \Exception('Item does not exist.');
        }

        // Check if proper branch
        if($item->branch_id != $order->branch_id) {
            throw new \Exception('Item is not available for the branch of order.');
        }

        if ($item->inventory) {
            // Check item for stocks in inventory
            $checkStock = $this->checkItemStock($item->inventory, $item->units, $qty);

            if ($checkStock['status'] == 'fail') {
                throw new \Exception("Item (name: $item->name) does not have enough stock.");
            }
        }

        // Retrieve product price
        $getProductPrice = $this->getProductPrice($item, $type);

        if ($getProductPrice['status'] == 'fail') {
            throw new \Exception($getProductPrice['message']);
        }

        $product_price = $getProductPrice['product_price'];

        // Sub-total of item ordered
        $added_item_subtotal = floatval($product_price) * intval($qty);

        // Validate Add-on
        if ($hasAddons) {
            // Validate Add-on
            $AddonService = new AddonService;
            $response = $AddonService->validateAddon($item, $isdinein, $qty);

            if (isset($response) && $response['status'] == 'fail') {
                throw new \Exception($response['message']);
            }
        }

        DB::beginTransaction();

        try {
            $data = [
                'is_dinein' => $isdinein ? true : false,
                'is_beans' => isset($item->is_beans) && $item->is_beans == 1 ? true : false,
                'grind_type' => isset($grind_type) ? $grind_type : null,
                'has_addons' => $hasAddons
            ];

            $orderItemId = OrderItem::generateUniqueId();

            $ord_item = new OrderItem();
            $ord_item->order_id = $order->order_id;
            $ord_item->order_item_id = $orderItemId;
            $ord_item->menu_id = $item->id;
            $ord_item->inventory_id = isset($item->inventory) ? $item->inventory->id : null;
            $ord_item->inventory_name = isset($item->inventory) ? $item->inventory->name : null;
            $ord_item->inventory_code = isset($item->inventory) ? $item->inventory->inventory_code : null;
            $ord_item->name = $item->name;
            $ord_item->from = isset($item->category) ? $item->category->from : null;
            $ord_item->price = $product_price;
            $ord_item->type = $type;
            $ord_item->unit_label =isset($item->inventory) ? $item->inventory->unit : null;
            $ord_item->units = $item->units;
            $ord_item->qty = $qty;
            $ord_item->data = $data;
            $ord_item->total_amount = $added_item_subtotal;
            $ord_item->status = $order->confirmed ? 'ordered' : 'pending';
            $ord_item->save();

            if ($order->confirmed) {
                $recordedItems = [];

                if ($item->inventory) {
                    // // Deduct to inventory for cart items
                    $deduct_inventory = $this->deductQtyToInventory($item->inventory, $item->units, $qty);

                    if ($deduct_inventory['status'] == 'fail') {
                        throw new \Exception("Failed to add order. Item (name: $item->name) does not have enough stock.");
                    }

                    $recordedItems = [
                        'branch_id' => $item->inventory->branch_id,
                        'branch_name' => $item->inventory->branch->name,
                        'inventory_id' => $item->inventory->id,
                        'inventory_code' => $item->inventory->inventory_code,
                        'name' => $item->inventory->name,
                        'unit_label' => $item->inventory->unit,
                        'total_qty' => -$deduct_inventory['deducted_stock'],
                        'stock_before' => $deduct_inventory['previous_stock'],
                        'stock_after' => $deduct_inventory['stock']
                    ];

                    InventoryLog::create([
                        'title' => 'Add Order Item',
                        'data' => [
                            'module' => 'order',
                            'section' => 'add-order-item',
                            'order_id' => $order->order_id,
                            'inventory' =>$recordedItems
                        ]
                    ]);
                }

                if (isset($ord_item->data['has_addons']) && $ord_item->data['has_addons'] == 1) {
                    $is_dinein = isset($ord_item->data['is_dinein']) && $ord_item->data['is_dinein'] == 1 ? true : false;
                    $addons = $ord_item->getAddonItems($is_dinein);

                    if (count($addons) > 0) {

                        foreach ($addons as $addon) {
                            // Deduct to inventory for cart items
                            $addon_qty = $addon->qty * $ord_item->qty;
                            $deduct_inventory = $this->deductQtyToInventory($addon->inventory, 1, $addon_qty);

                            if ($deduct_inventory['status'] == 'fail') {
                                $label = $addon->inventory->name;
                                throw new \Exception("Failed to add order. Item Addon (name: $label) does not have enough stock.");
                            }

                            $createAddOn = new AddonOrderItem;
                            $createAddOn->create([
                                'order_id' => $ord_item->order_id,
                                'order_item_id' => $ord_item->order_item_id,
                                'addon_id' => $addon->id,
                                'inventory_id' => $addon->inventory->id,
                                'inventory_name' => $addon->inventory->name,
                                'inventory_code'=> $addon->inventory->inventory_code,
                                'menu_id' => $addon->menu_id,
                                'unit' => 1,
                                'unit_label' => $addon->inventory->unit,
                                'qty' => $addon_qty,
                                'is_dinein' => $addon->is_dinein
                            ]);

                            $recordedItems = [
                                'branch_id' => $addon->inventory->branch_id,
                                'branch_name' => $addon->inventory->branch->name,
                                'inventory_id' => $addon->inventory->id,
                                'inventory_code' => $addon->inventory->inventory_code,
                                'name' => $addon->inventory->name,
                                'unit_label' => $addon->inventory->unit,
                                'total_qty' => -$deduct_inventory['deducted_stock'],
                                'stock_before' => $deduct_inventory['previous_stock'],
                                'stock_after' => $deduct_inventory['stock']
                            ];

                            InventoryLog::create([
                                'title' => 'Add Order Item',
                                'data' => [
                                    'module' => 'order',
                                    'section' => 'add-item',
                                    'order_id' => $order->order_id,
                                    'inventory' =>$recordedItems
                                ]
                            ]);
                        }
                    }
                }
            }

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->order_id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_type, $order->discount_unit, $order->fees, $order->deposit_bal, 0);

            // update subtotal of orders table
            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
            $order->discount_amount = round($orderInvoice['discount'], 2);
            $order->save();

            DB::commit();

            return [
                'status' => 'success',
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            ErrorLog::create([
                'location' => 'OrderService.addItem',
                'message' => $exception->getMessage()
            ]);

            throw new \Exception('Something went wrong. Please contact Administrator. (99)');
        }
    }


    /**
     *Update item to the order
     *
     * @param Object $order  order to update
     * @param Object $item  order item model to update
     * @param String $quantity quantity of order item
     * @param String $grind_type grind type of menu item (if beans)
     * @param Boolean $isdinein if dine-in or take-out order
     * @param Boolean $hasAddons has addons
     * @param String $note optional note
     * @return array|string
     */
    public function updateItem($order, $item, $quantity=0, $grind_type, $isdinein, $hasAddons, $note)
    {
        $unit_price = $item->price;

        if ($quantity <= 0) {
            throw new \Exception('Item quantity cannot be less than or equal to 0.');
        }


        if (!$item->menu) {
            throw new \Exception("Menu item for (name: {$item->name}) is unavailable. Please delete the item.");
        }

        $InventoryService = new InventoryService;
        $inventoriesUsed = $InventoryService->getInventoriesUsedByOrder($order->items);

        if ($item->inventory_id != null) {
            if (array_key_exists($item->inventory_id, $inventoriesUsed)) {
                // Check item for stocks in inventory
                $running_stock = $inventoriesUsed[$item->inventory_id]['running_stock'];
                $original_order_qty = $item->qty * $item->units;
                $inventory_used_less_current_item = $inventoriesUsed[$item->inventory_id]['total_used'] - $original_order_qty;
                $new_inventory_qty = $quantity * $item->units;

                $new_total = $inventory_used_less_current_item + $new_inventory_qty;
                if ($running_stock < $new_total) {
                    throw new \Exception("Inventory item for (name: {$item->name}) does not have enough stock.");
                }
            } else {
                throw new \Exception("Inventory item for (name: {$item->name}) is unavailable. Please delete the item.");
            }
        }

        if ($hasAddons) {
            $addons = MenuAddOn::where('menu_id', $item->menu_id)->where('is_dinein', $isdinein ? true : false)->get();

            // Validate Add-on
            if (count($addons) > 0) {
                foreach ($addons as $addon) {
                    if (array_key_exists($addon->inventory_id, $inventoriesUsed)) {

                        // Check item for stocks in inventory
                        $running_stock = $inventoriesUsed[$addon->inventory_id]['running_stock'];
                        $original_order_qty = $item->qty * $addon->qty;
                        $inventory_used_less_current_item = $inventoriesUsed[$addon->inventory_id]['total_used'] - $original_order_qty;

                        $new_inventory_qty = $quantity * $addon->qty;
                        $new_total = $inventory_used_less_current_item + $new_inventory_qty;

                        if ($running_stock < $new_total) {
                            throw new \Exception("Addon item (name: {$addon->inventory->name}) does not have enough stock.");
                        }
                    } else {
                        throw new \Exception("Item has an addon with invalid inventory. Please delete the item or disable addon.");
                    }
                }
            }
        }

        $data = $item->data ?? [];
        $data['is_dinein'] = $isdinein ? true : false;
        $data['has_addons'] = $hasAddons;

        if (isset($data['is_beans']) && $data['is_beans']) {
            $data['grind_type'] = $grind_type;
        }

        DB::beginTransaction();

        try {
            // Update total amount of item
            $item->total_amount = round(floatval($unit_price*$quantity), 2);
            $item->qty = $quantity;
            $item->data = $data;
            $item->note = $note;
            $item->save();


            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->order_id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_type, $order->discount_unit, $order->fees, $order->deposit_bal, 0);

            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
            $order->discount_amount = round($orderInvoice['discount'], 2);
            $order->save();

            DB::commit();
            return [
                'status' => 'success',
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            ErrorLog::create([
                'location' => 'OrderService.updateItem',
                'message' => $exception->getMessage()
            ]);

            throw new \Exception('Something went wrong. Please contact Administrator. (99)');
        }
    }

    /**
     * Delete item to the order
     *
     * @param Object $item  order item to delete
     * @param Object $order  order  to update
     * @param Array $addons attached Add-ons for the order item
     * @return array|string
     */
    public function deleteItem($item, $order)
    {
        DB::beginTransaction();

        try {
            // Delete order item
            $addOnItems = AddonOrderItem::where('order_item_id', $item->order_item_id)->delete();
            $item->delete();

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->order_id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_type, $order->discount_unit, $order->fees, $order->deposit_bal, 0);

            // update subtotal of orders table
            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
            $order->discount_amount = round($orderInvoice['discount'], 2);
            $order->save();

            DB::commit();

            return [
                'status' => 'success',
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            ErrorLog::create([
                'location' => 'OrderService.deleteItem',
                'message' => $exception->getMessage()
            ]);

            throw new \Exception('Something went wrong. Please contact Administrator. (99)');
        }

    }

    /**
     * void item to the order
     *
     * @param Object $item  order item to void
     * @param Object $order  order  to update
     * @return array|string
     */
    public function voidItem($item, $order)
    {
        DB::beginTransaction();

        try {
            // Delete order item
            $item->status = 'void';
            $item->save();

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->order_id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_type, $order->discount_unit, $order->fees, $order->deposit_bal, 0);

            // update subtotal of orders table
            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
            $order->discount_amount = round($orderInvoice['discount'], 2);
            $order->save();

            DB::commit();

            return [
                'status' => 'success',
            ];
        } catch (\Exception $exception) {
            DB::rollBack();

            ErrorLog::create([
                'location' => 'OrderService.deleteItem',
                'message' => $exception->getMessage()
            ]);

            throw new \Exception('Something went wrong. Please contact Administrator. (99)');
        }
    }

    /**
     * Calculate invoice of order total (adjust discount according to discount type)
     *
     * total_amount = (subtotal + fees) - discount
     * amount_given = cashgiven + deposit_bal
     * remaining_balance = amount_given - total_amount
     *
     * @param Object $order model of order
     *
     * @return array
     */
    public function calculateOrderInvoice($subtotal, $discount_type, $discount_unit, $fees, $deposit_bal, $cashgiven)
    {
        $discount = 0;
        if ($discount_type && $discount_unit) {
            if ($discount_type == 'percentage') {
                $percentage = $discount_unit / 100;
                $discount = ($subtotal + $fees) * $percentage;
            } else {
                $discount = $discount_unit;
            }
        }

        $total_amount = round(floatval($subtotal), 2) + round(floatval($fees), 2) - round(floatval($discount), 2);
        $amount_given = $deposit_bal + $cashgiven;
        $remaining_balance = $amount_given - $total_amount;

        return [
            'subtotal' => $subtotal,
            'fees' => $fees,
            'discount' => $discount,
            'total_amount' => $total_amount,
            'cashgiven' => $cashgiven,
            'deposit_balance' => $deposit_bal,
            'amount_given' => $amount_given,
            'remaining_balance' => $remaining_balance
        ];
    }

    /**
     * Calculate subtotal items price of order
     *
     * @param Object $order model of order
     *
     * @return array
     */
    public function updateOrderInvoice($order, $subtotal, $total_amount)
    {
        // update subtotal of orders table
        $order->subtotal = round(floatval($subtotal), 2);
        $order->total_amount = round($total_amount, 2);
        $order->save();

        return $subtotal;
    }


    /**
     * Calculate subtotal items price of order
     *
     * @param String $id order id of order item
     *
     * @return array
     */
    public function getOrderSubtotal($id)
    {
        $ord_item = new OrderItem();
        $subtotal = $ord_item->where('order_id', $id)->where('status', '!=', 'void')->sum('total_amount');

        return $subtotal;
    }

    /**
     * Calculate total invoice of order
     *
     * @param Object $menu model of the menu item
     * @param String $quantity Order quantity of the item
     *
     * @return array
     */
    // public function pay($order, $cashgiven)
    // {
    //     $total_amount = $order->subtotal - $order->discount_amount - $order->fees;
    //     $remaining_balance = $total_amount - $deposit_bal;


    //     return $subtotal;
    // }

    /**
     * Check check if item exist or have enough stock
     *
     * @param Object $inventory model of the menu item
     * @param String $units number of unit per qty the menu item
     * @param String $qty Order quantity of the item
     *
     * @return array
     */
    public function checkItemStock($inventory, $units, $qty)
    {
        // Check if enough stock
        $units_needed = $qty * $units;
        $item_stock = $inventory->stock ?? 0;

        if ($units_needed > $item_stock) {
            return [
                'status' => 'fail'
            ];
        }

        return [
            'status' => 'success',
            'stocks' => $item_stock
        ];
    }

    /**
     * Deduct the total units ordered to the inventory
     *
     * @param Object $item model of Inventory
     * @param String $units number of units per quantity
     * @param String $qty quantity of order item
     *
     *
     * @return array
     */
    public function deductQtyToInventory($item, $units, $qty)
    {
        $current_stock = $item->stock;
        $needed_stock = $units * $qty;
        $new_stock = $current_stock - $needed_stock;

        if ($new_stock < 0) {
            return [
                'status' => 'fail'
            ];
        }

        $item->stock = $new_stock;
        $item->previous_stock = $current_stock;
        $item->save();

        return [
            'status' => 'success',
            'deducted_stock' => $needed_stock,
            'stock' => $item->stock,
            'previous_stock' => $item->previous_stock
        ];
    }

    /**
     * Check and retrieve the product price of item
     *
     * @param Object $menu model of the menu item
     * @param String $type type of product
     *
     * @return array
     */
    public function getProductPrice($item, $type)
    {
        $product_price = null;

        if ($type == 'wholesale') {
            if ($item->wholesale_price == null && $item->wholesale_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a wholesale price."
                ];
            }
            $product_price = $item->wholesale_price;
        } else if ($type == 'regular') {
            if ($item->reg_price == null && $item->reg_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a regular price."
                ];
            }
            $product_price = $item->reg_price;
        } else if ($type == 'retail') {
            if ($item->retail_price == null && $item->retail_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a retail price."
                ];
            }
            $product_price = $item->retail_price;
        } else if ($type == 'rebranding') {
            if ($item->rebranding_price == null && $item->rebranding_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a rebranding price."
                ];
            }
            $product_price = $item->rebranding_price;
        } else if ($type == 'distributor') {
            if ($item->distributor_price == null && $item->distributor_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a distributor price."
                ];
            }
            $product_price = $item->distributor_price;
        } else {
            if ($item->reg_price == null && $item->reg_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Item (name: {$item->name}) does not have a regular price."
                ];
            }
        }

        return [
            'status' => 'success',
            'product_price' => $product_price
        ];
    }

}
