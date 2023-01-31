<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class DispatcherController extends Controller
{
    //
    public function index (Request $request)
    {

        $orders = Order::with(['items' => function ($query) {
            $query->with('addons');
            $query->where('dispatcher_cleared', false);
            $query->where('from', '=', 'kitchen');

        }])->whereHas('items', function ($query) {
            $query->where('dispatcher_cleared', false);
            $query->where('from', '=', 'kitchen');
        })
        ->where('cancelled', false)
        ->where('confirmed', true);

        if (auth()->user()->branch_id != null) {
            $orders = $orders->where('branch_id', auth()->user()->branch_id);
        }
        $orders = $orders->orderBy('created_at', 'desc')->get();

        return view('dispatch.index', compact('orders'));
    }

    public function serve (Request $request)
    {
        $item = OrderItem::where('id', $request->id)->first();

        if ($item) {
            if ($item->status != 'done') {
                return redirect()->route('dispatch.list')->with('error', 'Unable to serve item. Kitchen has yet to clear the item.');
            }

            $order = Order::where('order_id', $item->order_id)->first();
            $order->updated_at = Carbon::now();
            $order->save();

            $item->status = 'served';
            $item->served_by = auth()->user()->name;
            $item->save();
            return redirect()->route('dispatch.list')->with('success', 'Order item ' . $item->name . ' is  served.');
        }
        return redirect()->route('dispatch.list')->with('error', 'Order item has been removed.');
    }

    public function clear (Request $request)
    {
        $order = Order::where('order_id', $request->id)->first();

        if ($order) {
            OrderItem::where('order_id', $order->order_id)
                ->where('from', 'kitchen')
                ->where(function ($query) {
                    $query->where('status', 'done')
                        ->orWhere('status', 'void')
                        ->orWhere('status', 'served');
                })
                ->update(['dispatcher_cleared' => true]);

            OrderItem::where('order_id', $order->order_id)
                ->where('status', 'done')
                ->where('from', 'kitchen')
                ->update([
                    'status' => 'served',
                    'served_by' => auth()->user()->name
                ]);

            $order->updated_at = Carbon::now();
            $order->save();

            return back()->with('success', "order $order->order_id has been successfully cleared.");
        }
        return back()->with('error', 'Order does not exist.');
    }
}
