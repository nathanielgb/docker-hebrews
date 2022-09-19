<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;;
use App\Models\Customer;

class OrderReportController extends Controller
{
    public function showGenerateReport (Request $request)
    {
        $admins = User::where('type', '!=', 'SUPERADMIN')->pluck('name');
        $customers = Customer::all()->pluck('name');

        return view('order_reports.generate', compact('admins', 'customers'));
    }

    public function generate (Request $request)
    {
        $orders = Order::with('items')->where(function ($query) use ($request) {

            if ($request->date !== null) {
                $date_range = explode('-', str_replace(' ', '', $request->date));
                $start_date = Carbon::parse($date_range[0])->startOfDay();
                $end_date = Carbon::parse($date_range[1])->endOfDay();
                $query->whereBetween('updated_at', [$start_date, $end_date]);
            }
            if ($request->order_id !== null) {
                $_ord_numbers = str_replace(' ', '', $request->order_id);
                $ord_numbers = explode(',', $_ord_numbers);
                $query->whereIn('id', $ord_numbers);
            }
            if ($request->status !== null) {
                if ($request->status == 'pending') {
                    $query->where('pending', 1);
                } else if ($request->status == 'confirmed') {
                    $query->where('confirmed', 1);
                } else if ($request->status == 'completed') {
                    $query->where('completed', 1);
                } else if ($request->status == 'cancelled') {
                    $query->where('cancelled', 1);
                }
            }
            if ($request->servername !== null) {
                $query->where('server_name', 'LIKE', '%' . $request->servername . '%');
            }
            if ($request->customer_name !== null) {
                $query->where('customer_name', 'LIKE', '%' . $request->customer_name . '%');
            }
        });

        $orders_subtotal = $orders->sum('subtotal') ?? 0;
        $orders_discount = $orders->sum('discount_amount') ?? 0;
        $orders_total = $orders->sum('total_amount') ?? 0;
        $order_count = $orders->count() ?? 0;
        $date_range = $request->date;
        $status = $request->status;

        $order_numbers = $orders->pluck('id');
        $customers = $orders->pluck('customer_name');

        // Use this to be able to use group by, for some reason does not work without this line
        DB::statement("SET SQL_MODE=''");

        $order_items = DB::table('order_items')->select(DB::raw('order_id, menu_id, inventory_id, name, inventory_name, SUM(qty) AS total_qty, SUM(qty*units) AS stock_used, SUM(total_amount) AS total_amount'))
            ->whereIn('order_id', $order_numbers)
            ->groupBy('menu_id')->get();

            return view('order_reports.summary',compact(
            'orders_subtotal',
            'orders_discount',
            'orders_total',
            'order_count',
            'date_range',
            'status',
            'order_numbers',
            'order_items',
            'customers'
        ));
    }
}
