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
            $query->where('status', '!=', 'served');
            $query->where('from', '=', 'kitchen');

        }])->whereHas('items', function ($query) {
            $query->where('status', '!=', 'served');
            $query->where('from', '=', 'kitchen');
        })
        ->where('cancelled', false)
        ->where('completed', false)
        ->where('confirmed', true)
        ->orderBy('created_at', 'ASC')
        ->get();

        return view('dispatch.index', compact('orders'));
    }

    public function serve (Request $request)
    {
        $item = OrderItem::where('id', $request->id)->first();

        if ($item) {
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
}
