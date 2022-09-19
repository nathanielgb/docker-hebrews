<?php
namespace App\Services;

use App\Models\ErrorLog;
use App\Models\Menu;
use App\Models\MenuAddOn;
use App\Models\MenuInventory;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class OrderService
{

    /**
     *Add new item to the order
     *
     * @param Object $order model of current order
     * @param String $id menu id of the item being added
     * @param String $quantity order quantity of the item
     * @param String $type product type of menu item
     * @param Array $addons attached Add-ons for the menu item
     * @return array|string
     */
    public function addItem($order, $id, $qty, $type, $addons)
    {
        if ($order->confirmed) {
            throw new \Exception('Cannot add item for confirmed orders.');
        }

        $item = Menu::where('id', $id)->first();

        // Check if item exist
        if (!$item) {
            throw new \Exception('Item does not exist.');
        }

        // Check if item is already ordered
        $ord_item = OrderItem::where('order_id', $order->id)
            ->where('menu_id', $item->id)
            ->first();

        if ($ord_item) {
            return [
                'status' => 'warning',
                'message' => 'Item is already in the order.'
            ];
        }

        // Check item for stocks in inventory
        $checkStock = $this->checkItemStock($item, $item->units, $qty);

        if ($checkStock['status'] == 'fail') {
            throw new \Exception("Item (name: {$item->name}) does not have enough stock.");
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
        $AddonService = new AddonService;
        $response = $AddonService->validateAddon($addons);

        if (isset($response) && $response['status'] == 'fail') {
            throw new \Exception($response['message']);
        }

        DB::beginTransaction();

        try {

            // // Deduct to inventory for cart items
            // $deduct_inventory = $this->deductQtyToInventory($item->inventory, $item->units, $qty);

            // if ($deduct_inventory['status'] == 'fail') {
            //     return redirect()->back()->with('error', "Failed to generate order. Item $item->name does not have enough stock.");
            // }

            // foreach ($response['data'] as $addon) {
            //     $addonModel = MenuAddOn::where('id', $addon['addon_id'])->first();
            //     $deduct_addon = $this->deductQtyToInventory($addonModel->inventoryAddon, 1, $addon['qty']);

            //     if ($deduct_addon['status'] == 'fail') {
            //         return redirect()->back()->with('error', "Failed to generate order. Add-on Item $addon->name does not have enough stock.");
            //     }
            // }

            // Add item to order items table
            $ord_item = OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item->id,
                'inventory_id' => $item->inventory_id,
                'inventory_name' => $item->inventory->name,
                'name' => $item->name,
                'from' => $item->category->from,
                'price' => $product_price,
                'type' => $type,
                'unit_label' => $item->inventory->unit,
                'units' => $item->units,
                'qty' => $qty,
                'data' => $response['data'] ?? [],
                'total_amount' => $added_item_subtotal,
                'status' => 'pending',
            ]);

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_amount, $order->fees, $order->deposit_bal, 0);

            // update subtotal of orders table
            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
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
     * @param Array $addons attached Add-ons for the order item
     * @return array|string
     */
    public function updateItem($order, $item, $quantity=0, $addons)
    {
        $unit_price = $item->price;
        $new_qty = $quantity;

        if ($new_qty <= 0) {
            throw new \Exception('Item quantity cannot be less than or equal to 0.');
        }

        $inventory = MenuInventory::where('id', $item->inventory_id)->first();

        if (!$inventory) {
            throw new \Exception('Error updating quantity. Inventory item does not exist.');
        }

        // Check item for stocks in inventory
        $checkStock = $this->checkItemStock($item->menu, $item->units, $new_qty);

        if (isset($checkStock) &&  $checkStock['status'] == 'fail') {
            throw new \Exception("Item (name: {$item->name}) does not have enough stock.");
        }

        // Validate Add-on
        $AddonService = new AddonService;
        $response = $AddonService->validateAddon($addons);

        if (isset($response) && $response['status'] == 'fail') {
            throw new \Exception($response['message']);
        }

        DB::beginTransaction();

        try {
            // Update total amount of item
            $item->total_amount = round(floatval($unit_price*$new_qty), 2);
            $item->qty = $new_qty;
            $item->data = $response['data'] ?? [];
            $item->save();

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_amount, $order->fees, $order->deposit_bal, 0);

            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
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
            $item->delete();

            // Re-calculate total order price
            $new_subtotal = $this->getOrderSubtotal($order->id);
            $orderInvoice = $this->calculateOrderInvoice($new_subtotal, $order->discount_amount, $order->fees, $order->deposit_bal, 0);

            // update subtotal of orders table
            $order->subtotal = round(floatval($new_subtotal), 2);
            $order->total_amount = round($orderInvoice['total_amount'], 2);
            $order->remaining_bal = round($orderInvoice['remaining_balance'], 2);
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
     * Calculate invoice of order total
     *
     * total_amount = (subtotal + fees) - discount
     * amount_given = cashgiven + deposit_bal
     * remaining_balance = amount_given - total_amount
     *
     * @param Object $order model of order
     *
     * @return array
     */
    public function calculateOrderInvoice($subtotal, $discount, $fees, $deposit_bal, $cashgiven)
    {
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
        $subtotal = OrderItem::where('order_id', $id)->sum('total_amount');

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
     * @param Object $menu model of the menu item
     * @param String $units model of the menu item
     * @param String $qty Order quantity of the item
     *
     * @return array
     */
    public function checkItemStock($menu, $units, $qty)
    {
        // Check if enough stock
        $units_needed = $qty * $units;
        $item_stock = $menu->inventory->stock ?? 0;

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
                if ($item->reg_price == null && $item->reg_price == 0) {
                    return [
                        'status' => 'fail',
                        'message' => "Item (name: {$item->name}) does not have a retail price."
                    ];
                }
            }
            $product_price = $item->retail_price;
        } else if ($type == 'rebranding') {
            if ($item->rebranding_price == null && $item->rebranding_price == 0) {
                if ($item->reg_price == null && $item->reg_price == 0) {
                    return [
                        'status' => 'fail',
                        'message' => "Item (name: {$item->name}) does not have a rebranding price."
                    ];
                }
            }
            $product_price = $item->rebranding_price;
        } else if ($type == 'distributor') {
            if ($item->distributor_price == null && $item->distributor_price == 0) {
                if ($item->reg_price == null && $item->reg_price == 0) {
                    return [
                        'status' => 'fail',
                        'message' => "Item (name: {$item->name}) does not have a distributor price."
                    ];
                }
            }
            $product_price = $item->distributor_price;
        } else {
            if ($item->reg_price == null && $item->reg_price == 0) {
                return [
                    'status' => 'fail',
                    'message' => "Error encountered please inform Administrator."
                ];
            }
        }

        return [
            'status' => 'success',
            'product_price' => $product_price
        ];
    }

}
