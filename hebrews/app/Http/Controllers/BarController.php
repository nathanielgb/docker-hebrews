<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarController extends Controller
{
    //
    public function index (Request $request)
    {
        $orders = Order::with(['items' => function ($query) {

            $query->where('from', '=', 'bar');
            $query->where('status', '!=', 'done');
            $query->where('status', '!=', 'served');

        }])->whereHas('items', function ($query) {
            $query->where('from', '=', 'bar');
            $query->where('status', '!=', 'done');
            $query->where('status', '!=', 'served');

        })
        ->where('cancelled', false)
        ->where('completed', false)
        ->orderBy('created_at', 'ASC')
        ->get();

        return view('bar.index', compact('orders'));
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
            return redirect()->route('bar.orders.list')->with('success', 'Order item ' . $item->name . ' is being prepared for table ' . implode(',',$order->table) . '.');
        }
        return redirect()->route('bar.orders.list')->with('error', 'Order item has been removed.');
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
            return redirect()->route('bar.orders.list')->with('success', 'Order item ' . $item->name . ' is ready to serve for ' . implode(',',$order->table) . '.');
        }
        return redirect()->route('bar.orders.list')->with('error', 'Order item has been removed.');
    }
}
