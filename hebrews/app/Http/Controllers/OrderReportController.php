<?php

namespace App\Http\Controllers;

use App\Models\AddonOrderItem;
use App\Models\Branch;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;;
use App\Models\Customer;
use App\Models\Expense;

class OrderReportController extends Controller
{
    public function showGenerateReport (Request $request)
    {
        $admins = User::where('type', '!=', 'SUPERADMIN')->pluck('name');
        $customers = Customer::all()->pluck('name');
        $branches = Branch::all();

        return view('order_reports.generate', compact('admins', 'customers', 'branches'));
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
                $query->whereIn('order_id', $ord_numbers);
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
            if ($request->branch_id !== null) {
                $query->where('branch_id', $request->branch_id);
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
        $branch_id = $request->branch_id;

        $order_numbers = $orders->pluck('order_id');
        // $customers = $orders->pluck('customer_name');
        $customers = $request->customer_name;

        // Use this to be able to use group by, for some reason does not work without this line
        DB::statement("SET SQL_MODE=''");

        $order_items = DB::table('order_items')->select(DB::raw('order_id, menu_id, inventory_id, name, inventory_name, inventory_code, unit_label, SUM(qty) AS total_qty, SUM(qty*units) AS stock_used, SUM(total_amount) AS total_amount'))
            ->whereIn('order_id', $order_numbers)
            ->groupBy('menu_id')->get();

        $addon_order_items = AddonOrderItem::select(DB::raw('order_id, order_item_id, addon_id, inventory_id, inventory_name, inventory_code, unit_label, SUM(qty) AS stock_used'))
            ->whereIn('order_id', $order_numbers)
            ->groupBy('addon_id')->get();

        // Get expenses
        $expenses = Expense::where(function ($query) use ($request) {
            if ($request->date) {
                $date_range = explode('-', str_replace(' ', '', $request->date));
                $start_date = Carbon::parse($date_range[0])->startOfDay();
                $end_date = Carbon::parse($date_range[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }
        });

        $total_expense = $expenses->sum('amount');

        // Calculate net profit
        $profit = $orders_total - $total_expense;

        return view('order_reports.summary',compact(
        'orders_subtotal',
        'orders_discount',
        'orders_total',
        'total_expense',
        'profit',
        'order_count',
        'date_range',
        'status',
        'order_numbers',
        'order_items',
        'addon_order_items',
        'customers',
        'branch_id'
        ));
    }

    public function showExpenseReport(Request $request)
    {

        $date_range = Carbon::today()->format('Y/m/d') . ' - ' . Carbon::today()->format('Y/m/d');
        $expenses = Expense::whereDate('created_at', '>=',Carbon::today());

        if ($request->except(['page'])) {

            $expenses = Expense::where(function ($query) use ($request) {
                if ($request->name) {
                    $query->where('name', 'LIKE', "%$request->name%");
                }
                if ($request->date  && $request->date !== 'all') {
                    $date_range = explode('-', str_replace(' ', '', $request->date));
                    $start_date = Carbon::parse($date_range[0])->startOfDay();
                    $end_date = Carbon::parse($date_range[1])->endOfDay();
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                }
            });

        }

        $expenses = $expenses->orderBy('created_at', 'DESC')->paginate(20);
        $total_expense = $expenses->sum('amount');

        return view('expense_reports.index', compact('expenses','date_range','total_expense'));
    }

    public function addExpense(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:300',
            'amount' => 'required|numeric|min:1|max:9999999',
        ]);


        $expense = new Expense;
        $expense->name = $request->name;
        $expense->amount = $request->amount;
        $expense->save();

        return back()->with('success', 'Successfully added ' . $request->name . '.');
    }

    public function deleteExpense(Request $request)
    {
        $expense = Expense::where('id', $request->id)->first();

        if ($expense) {
            $expense->delete();
            return back()->with('success', 'Successfully deleted expense.');
        }
        return back()->with('error', 'Record does not exist.');
    }
}
