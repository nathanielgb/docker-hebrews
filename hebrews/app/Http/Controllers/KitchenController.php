<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    //

    public function index (Request $request)
    {
        $orders = Order::with(['items' => function ($query) {
            $query->with('addons');
            $query->where('from', '=', 'kitchen');
            $query->where('kitchen_cleared', false);

        }])->whereHas('items', function ($query) {
            $query->where('from', '=', 'kitchen');
            $query->where('kitchen_cleared', false);

        })
        ->where('confirmed', true)
        ->where('cancelled', false);

        if (auth()->user()->branch_id != null) {
            $orders = $orders->where('branch_id', auth()->user()->branch_id);
        }
        $orders = $orders->orderBy('created_at', 'desc')->get();

        return view('kitchen.index', compact('orders'));
    }

    public function prepare (Request $request)
    {
        $item = OrderItem::where('id', $request->item_id)->first();

        if ($item) {
            $order = Order::where('order_id', $item->order_id)->first();
            $order->updated_at = Carbon::now();
            $order->save();

            $item->status = 'preparing';
            $item->save();
            return redirect()->route('kitchen.orders.list')->with('success', 'Order item ' . $item->name . ' is being prepared.');
        }
        return redirect()->route('kitchen.orders.list')->with('error', 'Order item has been removed.');
    }


    public function done (Request $request)
    {
        $item = OrderItem::where('id', $request->id)->first();

        if ($item) {
            $order = Order::where('order_id', $item->order_id)->first();
            $order->updated_at = Carbon::now();
            $order->save();

            $item->status = 'done';
            $item->save();
            return redirect()->route('kitchen.orders.list')->with('success', 'Order item ' . $item->name . ' is ready to serve.');
        }
        return redirect()->route('kitchen.orders.list')->with('error', 'Order item has been removed.');
    }

    public function complete (Request $request)
    {
        $order = Order::where('order_id', $request->id)->first();

        if ($order) {
            $pending_items = OrderItem::where('order_id', $order->order_id)
                        ->where('from', 'kitchen')
                        ->where('status', '!=', 'done')
                        ->count();

            if ($pending_items > 0) {
                return redirect()->route('kitchen.orders.list')->with('error', 'There are still pending orders left. ID ' . $order->order_id);
            }

            $order->completed = true;
            $order->updated_at = Carbon::now();
            $order->save();
            return redirect()->route('kitchen.orders.list')->with('success', 'Order ID ' . $order->order_id .' is completed. ');

        }
        return redirect()->route('kitchen.orders.list')->with('error', 'Order does not exist.');
    }

    public function clear (Request $request)
    {
        $order = Order::where('order_id', $request->id)->first();

        if ($order) {
            OrderItem::where('order_id', $order->order_id)
                ->where('from', 'kitchen')
                ->update(['kitchen_cleared' => true]);

            OrderItem::where('order_id', $order->order_id)
                ->where('status', '!=', 'void')
                ->where('status', '!=', 'served')
                ->where('from', 'kitchen')
                ->update(['status' => 'done']);

            $order->updated_at = Carbon::now();
            $order->save();

            return back()->with('success', "order $order->order_id has been successfully cleared.");
        }
        return back()->with('error', 'Order does not exist.');
    }
}
