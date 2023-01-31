<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    //
    public function index (Request $request)
    {
        $orders = Order::with(['items' => function ($query) {
            $query->with('addons');
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');

        }])->whereHas('items', function ($query) {
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');
        })
        ->where('cancelled', false)
        ->where('confirmed', true);


        if (auth()->user()->branch_id != null) {
            $orders = $orders->where('branch_id', auth()->user()->branch_id);
        }
        $orders = $orders->orderBy('created_at', 'desc')->get();

        return view('production.index', compact('orders'));
    }

    public function prepare (Request $request)
    {
        $item = OrderItem::where('id', $request->item_id)->first();

        if ($item) {
            if ($item->status == 'void') {
                return back()->with('error', 'Failed to prepare order item ' . $item->name . '. item is voided.');
            }

            $order = Order::where('order_id', $item->order_id)->first();
            $order->updated_at = Carbon::now();
            $order->save();

            $item->status = 'preparing';
            $item->save();
            return back()->with('success', 'order item ' . $item->name . ' is being prepared.');
        }
        return back()->with('error', 'Order item has been removed.');
    }

    public function done (Request $request)
    {
        $item = OrderItem::where('id', $request->id)->first();

        if ($item) {
            if ($item->status == 'void') {
                return back()->with('error', 'Failed to complete order item ' . $item->name . '. item is voided.');
            }

            $order = Order::where('order_id', $item->order_id)->first();
            $order->updated_at = Carbon::now();
            $order->save();

            $item->status = 'done';
            $item->served_by = auth()->user()->name;
            $item->save();
            return back()->with('success', 'order item ' . $item->name . ' is completed.');
        }
        return back()->with('error', 'Order item has been removed.');
    }

    public function clear (Request $request)
    {
        $order = Order::where('order_id', $request->id)->first();

        if ($order) {
            OrderItem::where('order_id', $order->order_id)
                ->where('from', 'storage')
                ->update(['production_cleared' => true]);

            OrderItem::where('order_id', $order->order_id)
                ->where('status', '!=', 'void')
                ->where('from', 'storage')
                ->update(
                    ['status' => 'done'],
                    ['served_by' => auth()->user()->name]
                );

            $order->updated_at = Carbon::now();
            $order->save();

            return back()->with('success', "order $order->order_id has been successfully cleared.");
        }
        return back()->with('error', 'Order does not exist.');
    }
}
