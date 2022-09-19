<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MenuInventory;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    //
    public function index (Request $request)
    {
        $order_items = $request->items;

        if (empty($order_items)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No order item entered.'
            ]);
        }

        // Get total quantity of each item
        $total = [];
        foreach ($order_items as $item) {
            $total[$item['name']] = isset($total[$item['name']]) ? $total[$item['name']] += $item['qty'] : $total[$item['name']] = $item['qty'];
        }

        // Check if the order items are available
        foreach ($total as $o_name => $o_qty) {
            $menu_item = Menu::with('inventory')->where('name', $o_name)->first();

            // Check if the item exist
            if (!$menu_item) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Menu Item ' . $o_name . ' is unavailable.'
                ]);
            }

            // Check if enough inventory
            if ($o_qty > $menu_item->inventory->stock) {
                return response()->json([
                    'status' => 'fail',
                    'message' => $menu_item->inventory->stock. ' ' . $menu_item->name . ' left, not enough stock.'
                ]);
            }
        }

        // Check if there is table selected when order type is dine-in
        if (empty($request->tables)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Please enter table/s for Dine-in customers.'
            ]);
        }

        // Insert Discount and other fees logic here
        $discounted_amount = 0;
        $fees = 0;

        // Total amount computation
        $total_order_price = floatval($request->subtotal) - floatval($discounted_amount) - floatval($fees);

        // Save order
        $order = new Order;
        $order->server_name = Auth::user()->name;
        $order->table = $request->tables;
        $order->subtotal = $request->subtotal;
        $order->discount_amount = 0;
        $order->fees = 0;
        $order->total_amount = round(floatval($total_order_price), 2);
        $order->order_type = $request->order_type;
        $order->discount_type = $request->discount_type;
        $order->pending = true;
        $order->note = $request->note;
        $order->save();

        $save_items = [];

        // Save order items and update inventory
        foreach($order_items as $ord_item) {
            $save_items[] = [
                'order_id' => $order->id,
                'menu_id' => $ord_item['menu_id'],
                'name' => $ord_item['name'],
                'from' => $ord_item['from'],
                'dinein_price' => floatval($ord_item['dinein_price']),
                'takeout_price' => floatval($ord_item['takeout_price']),
                'qty' => intval($ord_item['qty']),
                'order_type' => $ord_item['type'],
                'total_amount' => round(floatval($ord_item['total_price']), 2),
                'status' => 'ordered',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Update Menu Inventory stock
            $item_inventory = MenuInventory::where('menu_id', $ord_item['menu_id'])->first();
            $item_stock = $item_inventory->stock;
            $item_inventory->update([
                'stock' => intval($item_stock) - intval($ord_item['qty'])
            ]);
        }

        DB::table('order_items')->insert($save_items);


        return response()->json([
            'status' => 'success',
            'message' =>'Order ' . $order->id . ' is sucessfully created. Order is now being prepared.',
            'redirect' => route('order.show_summary', $order->id)
        ]);
    }

    public function findItem ($items, $itemTofindID)
    {
        $itemFound = null;

        foreach ($items as $_item) {
            if ($itemTofindID == $_item->menu_id) {
                $itemFound = $_item;
                break;
            }
        }

        return $itemFound;
    }
}
