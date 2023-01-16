<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Order;
use App\Models\MenuCategory;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PayOrderRequest;
use App\Models\AddonOrderItem;
use App\Models\BankTransaction;
use App\Models\ErrorLog;
use App\Models\InventoryLog;
use App\Services\OrderService;

class OrderController extends Controller
{
    // public function showTakeOrder()
    // {
    //     $categories = MenuCategory::with(['menus' => function ($query) {
    //         $query->whereHas('inventory', function ($q) {
    //             $q->where('stock', '>', 0);
    //         });
    //     }])->whereHas('menus', function ($query) {
    //         $query->whereHas('inventory', function ($q) {
    //             $q->where('stock', '>', 0);
    //         });
    //     });

    //     $categories = $categories->orderBy('name', 'ASC')->get()->toJson();

    //     return view('orders.take_order', compact('categories'));
    // }

    public function showOrders(Request $request)
    {
        if (auth()->user()->branch_id) {
            $orders = Order::where('branch_id', auth()->user()->branch_id)->where('cancelled', '!=', 1);

        } else {
            $orders = Order::where('cancelled', '!=', 1);
        }

        if ($request->except(['page'])) {

            $orders = Order::where(function ($query) use ($request) {
                if ($request->order_id) {
                    $query->where('order_id', 'LIKE', "%$request->order_id%");
                }
                if ($request->branch_id) {
                    $query->where('branch_id', $request->branch_id);
                }
                if ($request->cust_name) {
                    $query->where('customer_name', 'LIKE', "%$request->cust_name%");
                }
                if ($request->status) {
                    $status = $request->status;
                    if ($status == 'pending') {
                        $query->where('pending', 1);
                    }
                    if ($status == 'pending') {
                        $query->where('confirmed', 1);
                    }
                    if ($status == 'cancelled') {
                        $query->where('cancelled', 1);
                    }
                    if ($status == 'completed') {
                        $query->where('completed', 1);
                    }
                }
                if ($request->date) {
                    $date_range = explode('-', str_replace(' ', '', $request->date));
                    $start_date = Carbon::parse($date_range[0])->startOfDay();
                    $end_date = Carbon::parse($date_range[1])->endOfDay();
                    $query->whereBetween('updated_at', [$start_date, $end_date]);
                }
            });

            if ($request->filter == 'pending') {
                $orders = Order::where('pending', true)->where('completed', false);
            } elseif ($request->filter == 'completed') {
                $orders = Order::where('pending', false)->where('completed', true);
            }
        }
            $orders = $orders->orderBy('created_at', 'desc')->paginate(20);

        return view('orders.list', compact('orders'));
    }

    public function showSummary(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->with('items')->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->with('items')->first();
        }

        if ($order) {
            $accounts = BankAccount::all();

            $onProgressItems = OrderItem::where('order_id', $order->order_id)
            ->where('status', 'served')
            ->count();

            return view('orders.order_summary',compact('order','onProgressItems','accounts'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function printSummary(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->with('items')->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->with('items')->first();
        }

        if ($order) {
            return view('orders.sections.summary_print',compact('order'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }
    public function showAddOrderItem(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->first();
        }

        $menus = Menu::whereHas('inventory', function ($q) use ($order) {
            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', $order->branch_id);
            }
        });

        if ($order) {
            if ($order->confirmed) {
                return redirect()->back()->with('error', 'Cannot add item for confirmed orders.');
            }
            if ($order->paid) {
                return redirect()->back()->with('error', 'Cannot add item for paid orders.');
            }

            return view('orders.sections.add_item',compact('order','menus'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function addOrderItem(Request $request, $order_id,  OrderService $orderService)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $order_id)->first();
        } else {
            $order = Order::where('order_id', $order_id)->first();
        }

        if ($order) {
            $request->validate([
                'menuitem' => 'required|exists:menus,id',
                'type' => 'required',
                'quantity' => 'required|numeric|min:1'
            ]);
            try {
                $addItem = $orderService->addItem($order, $request->menuitem, $request->quantity, $request->type, $request->orderItemAddon);

                if ($addItem['status'] == 'warning') {
                    return redirect()->back()->with('warning', $addItem['message']);
                }

                return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order item added successfully.');
            } catch (\Exception $exception) {
                //catch $exception;
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function showOrderItems (Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->first();
        }

        $order_id = $request->order_id;
        $order_items = OrderItem::where('order_id', $order_id)->get();

        return view('orders.sections.show_order_items', compact('order', 'order_items', 'order_id'));
    }

    public function showEditOrderItems (Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->first();
        }

        $order_id = $request->order_id;
        $order_items = OrderItem::where('order_id', $order_id)->get();

        return view('orders.sections.edit_items', compact('order', 'order_items', 'order_id'));
    }

    public function updateOrderItems (Request $request, OrderService $orderService)
    {
        $request->validate([
            'quantity' => 'nullable|integer'
        ]);

        $item = OrderItem::where('id', $request->item_id)->first();

        if ($item) {
            if (auth()->user()->branch_id) {
                $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->first();
            } else {
                $order = Order::where('order_id', $item->order_id)->first();
            }

            if ($order) {
                if ($order->confirmed) {
                    return redirect()->back()->with('error', 'Order is confirmed, cannot change order.');
                }

                try {
                    $response = $orderService->updateItem($order, $item, $request->quantity, $request->orderItemAddon);

                    return redirect()->back()->with('success', 'Order Item ID  ' . $item->id . ' is updated successfully.');
                } catch (\Exception $exception) {
                    //throw $th;
                    return redirect()->back()->with('error', $exception->getMessage());
                }
            }
            return redirect()->route('order.list')->with('error', 'Order does not exist.');
        }
        return redirect()->back()->with('error', 'Order item no longer exist.');
    }


    public function deleteOrderItem (Request $request, OrderService $orderService)
    {
        $order_item = OrderItem::where('id', $request->id)->first();

        if ($order_item) {
            if (auth()->user()->branch_id) {
                $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $order_item->order_id)->first();
            } else {
                $order = Order::where('order_id', $order_item->order_id)->first();
            }

            if ($order) {
                if ($order->confirmed) {
                    return redirect()->back()->with('error', 'Order is confirmed, cannot change order.');
                }

                try {
                    $response = $orderService->deleteItem($order_item, $order);

                    return redirect()->back()->with('success', 'Order item is removed successfully.');
                } catch (\Exception $exception) {
                    //throw $th;
                    return redirect()->back()->with('error', $exception->getMessage());
                }
            }
            return redirect()->route('order.list')->with('error', 'Order does not exist.');
        }
        return redirect()->back()->with('error', 'Order item no longer exist.');
    }

    public function showPayForm (Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->first();
        $account = BankAccount::where('id', $order->bank_id)->first();

        if ($order) {
            if ($order->cancelled) {
                return redirect()->back()->with('error', 'Order is cancelled.');
            } elseif ($order->completed) {
                return redirect()->back()->with('success', 'Order is already completed.');
            }
            return view('orders.pay', compact('order','account'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function pay (PayOrderRequest $request, $id)
    {
        $order = Order::where('order_id', $id)->first();
        if ($order) {
            if ($order->cancelled) {
                return redirect()->back()->with('error', 'Order is cancelled.');
            } elseif ($order->completed) {
                return redirect()->back()->with('success', 'Order is already completed.');
            }

            $amount_given = floatval($request->input_amt);
            $confirmed_amount = $order->confirmed_amount + $amount_given;
            $total_amount = floatval($order->total_amount);
            $remaining_balance = $confirmed_amount - $total_amount;
            if (!$request->account) {
                return redirect()->back()->with('error', 'Credit to Bank Account is required.');
            }
            DB::beginTransaction();
            try {
                // Save the transaction if there is bank account selected
                if ($request->account) {
                    $account = BankAccount::where('id', $request->account)->first();
                    if ($account) {
                        if ($remaining_balance < 0) {
                            $amount_credited = $amount_given;
                        } else {
                            $amount_credited =  $amount_given - $remaining_balance;
                        }

                        $prev_bal = $account->bal;
                        $new_bal = $prev_bal + $amount_credited;

                        $account->update([
                            'bal' => $new_bal,
                        ]);

                        // Save transaction record
                        BankTransaction::create([
                            'order_id' => $order->order_id,
                            'account_id' => $account->id,
                            'action' => 'Order Payment',
                            'amount' => $amount_credited,
                            'running_bal' => $new_bal,
                            'prev_bal' => $prev_bal
                        ]);
                    }
                }

                $order->amount_given = $order->amount_given + $amount_given;
                $order->confirmed_amount = $confirmed_amount;
                $order->remaining_bal = $remaining_balance;
                $order->paid = true;
                $order->pending = false;
                $order->credited_by = auth()->user()->name;
                $order->save();
                DB::commit();

                return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully paid.');

            } catch (\Exception $exception) {
                //catch $exception;
                DB::rollBack();
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    // Edit Fees, Discounts, Initial Deposit and others
    public function edit (Request $request, $id, OrderService $orderService)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $id)->first();
        } else {
            $order = Order::where('order_id', $id)->first();
        }

        if ($order) {
            $request->validate([
                'order_type' => 'required|string',
                'delivery_method' => 'nullable|string',
                'fees' => 'nullable|numeric|min:0|max:9999999',
                'custom_discount' => 'nullable|numeric|min:0|max:9999999',
                'deposit' => 'nullable|numeric|min:0|max:9999999',
            ]);

            if ($request->custom_discount_toggle) {
                $discount_type = 'custom';
                $discount_unit = $request->custom_discount ?? 0;
            } else {
                $discount_type = $order->discount_type;
                $discount_unit = $order->discount_unit ?? 0;
            }

            $subtotal = $order->subtotal;
            $fees = $request->fees ?? 0;
            $deposit = $request->deposit ?? 0;

            $invoice = $orderService->calculateOrderInvoice($subtotal, $discount_type, $discount_unit, $fees, $deposit, 0);

            if ($invoice['discount'] > ($invoice['subtotal'] + $invoice['fees'])) {
                return back()->with('error', "Discount amount cannot be greater than the order total.");
            }

            DB::beginTransaction();
            try {
                if ($request->custom_discount_toggle) {
                    $order->discount_type = 'custom';
                    $order->discount_unit = $request->custom_discount ?? 0;
                }
                $order->order_type = $request->order_type;
                $order->table = $request->tables;
                $order->delivery_method = $request->delivery_method;
                $order->discount_amount = $invoice['discount'];
                $order->fees = $invoice['fees'];
                $order->total_amount = $invoice['total_amount'];
                $order->deposit_bal = $invoice['deposit_balance'];
                $order->confirmed_amount = $invoice['amount_given'];
                $order->remaining_bal = $invoice['remaining_balance'];
                $order->save();
                DB::commit();

                return redirect()->back()->with('success', 'Successfully updated order details.');
            } catch (\Exception $exception) {
                //catch $exception;
                DB::rollBack();
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function confirm (Request $request, $id, OrderService $orderService)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $id)->first();
        } else {
            $order = Order::where('order_id', $id)->first();
        }

        if ($order) {
            $acccount = BankAccount::where('id', $request->account)->first();

            if (!$acccount) {
                return redirect()->back()->with('error', 'Failed to confirm order. Bank account does not exist.');
            }

            DB::beginTransaction();
            try {
                // Recheck stock of items
                $ord_items = OrderItem::where('order_id', $order->order_id)->get();

                foreach ($ord_items as $ord_item) {
                    $menu = $ord_item->menu;

                    // Deduct to inventory for order items
                    $deduct_inventory = $orderService->deductQtyToInventory($menu->inventory, $ord_item->units, $ord_item->qty);

                    if ($deduct_inventory['status'] == 'fail') {
                        return redirect()->back()->with('error', "Order Item $ord_item->name does not have enough stock.");
                    }

                    if (isset($ord_item->data)) {
                        foreach ($ord_item->data as $addon) {
                            $orderAddonItem = AddonOrderItem::where('order_item_id', $ord_item->order_item_id)
                                ->where('addon_id', $addon['addon_id'])->first();
                            $deduct_addon = $orderService->deductQtyToInventory($orderAddonItem->addon->inventory, 1, $addon['qty']);

                            if ($deduct_addon['status'] == 'fail') {
                                return redirect()->back()->with('error', "Add-on Item $addon->name does not have enough stock.");
                            }

                            InventoryLog::create([
                                'title' => 'Order Confirmation',
                                'data' => [
                                    'section' => "addon-order-item",
                                    'type' => "deduct",
                                    'order_id' => $orderAddonItem->order_id,
                                    'addon_order_item_id' =>$orderAddonItem->id,
                                    'addon_name' => $orderAddonItem->name,
                                    'addon_id' => $orderAddonItem->addon_id,
                                    'inventory_id' => $orderAddonItem->inventory_id,
                                    'inventory_code' => $orderAddonItem->inventory_code,
                                    'units' => $orderAddonItem->units,
                                    'order_qty' => $orderAddonItem->qty,
                                    'stock_deducted' => $deduct_addon['deducted_stock'],
                                    'stock_before_deduction' => $deduct_addon['previous_stock'],
                                    'stock_after_deduction' => $deduct_addon['stock']
                                ]
                            ]);
                        }
                    }

                    // if ($ord_item->from == 'storage') {
                    //     $ord_item->status = 'done';
                    // } else {
                    //     $ord_item->status = 'ordered';
                    // }

                    $ord_item->status = 'ordered';

                    InventoryLog::create([
                        'title' => 'Order Confirmation',
                        'data' => [
                            'section' => "order-item",
                            'type' => "deduct",
                            'order_id' => $ord_item->order_id,
                            'order_item_id' =>$ord_item->id,
                            'order_item_name' => $ord_item->name,
                            'inventory_id' => $ord_item->inventory_id,
                            'inventory_code' => $ord_item->inventory_code,
                            'units' => $ord_item->units,
                            'order_qty' => $ord_item->qty,
                            'stock_deducted' => $deduct_inventory['deducted_stock'],
                            'stock_before_deduction' => $deduct_inventory['previous_stock'],
                            'stock_after_deduction' => $deduct_inventory['stock']
                        ]
                    ]);

                    $ord_item->save();
                }

                // Save the transaction if there is bank account selected
                if ($request->account) {
                    $account = BankAccount::where('id', $request->account)->first();
                    if ($account && $order->confirmed_amount > 0) {
                        $prev_bal = $account->bal;
                        $new_bal = $prev_bal + $order->confirmed_amount;

                        $account->update([
                            'bal' => $new_bal,
                        ]);

                        // Save transaction record
                        BankTransaction::create([
                            'order_id' => $order->order_id,
                            'account_id' => $account->id,
                            'action' => 'Confirmed Order (Initial Deposit)',
                            'amount' => $order->confirmed_amount,
                            'running_bal' => $new_bal,
                            'prev_bal' => $prev_bal
                        ]);
                    }
                }

                // Tag order as confirmed
                $order->confirmed = true;
                $order->bank_id = $account->id;
                $order->confirmed_by = auth()->user()->name;
                $order->save();

                DB::commit();

                return redirect()->back()->with('success', 'Successfully confirmed order.');

            } catch (\Exception $exception) {
                //catch $exception;
                DB::rollBack();

                ErrorLog::create([
                    'location' => 'OrderController.confirm',
                    'message' => $exception->getMessage()
                ]);

                return redirect()->back()->with('error', $exception->getMessage());
            }



            $order->confirmed = true;


        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function cancel (Request $request, $id)
    {
        $order = Order::with('items')->where('order_id', $id)->first();
        if ($order) {
            if ($order->cancelled) {
                return redirect()->route('order.list')->with('error', 'Order is already cancelled.');
            }

            if ($order->completed) {
                return redirect()->route('order.list')->with('error', 'Cannot cancel completed orders.');
            }

            $request->validate([
                'reason' => 'required|string|min:10|max:300',
            ]);

            $order->pending = false;
            $order->completed = false;
            $order->cancelled = true;
            $order->reason = $request->reason;
            $order->cancelled_by = auth()->user()->name;
            $order->save();

            return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully cancelled.');
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function complete (Request $request, $id)
    {
        $order = Order::with('items')->where('order_id', $id)->first();

        if ($order) {
            if ($order->cancelled) {
                return redirect()->route('order.list')->with('error', 'Cannot complete cancelled orders.');
            }

            if ($order->completed) {
                return redirect()->route('order.list')->with('error', 'Order is already completed.');
            }

            $order->pending = false;
            $order->completed = true;
            $order->cancelled = false;
            $order->save();

            return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully completed.');
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');

    }

    public function print (Request $request)
    {

        $order = Order::with('items')->where('order_id', $request->order_id)->first();

        if ($order) {
            $total_amount = number_format($order->total_amount, 2);
            $amount_given = number_format($order->amount_given, 2);
            $cashback = number_format(floatval($order->amount_given) - floatval($order->total_amount), 2);

            return view('orders.print_receipt', compact('order','total_amount','amount_given','cashback'));

            return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully paid.');
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');

    }
}
