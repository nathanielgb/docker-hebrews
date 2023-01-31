<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Order;
use App\Models\ErrorLog;
use App\Models\OrderItem;
use App\Models\BankAccount;
use App\Models\InventoryLog;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\AddonOrderItem;
use App\Services\OrderService;
use App\Models\BankTransaction;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMenuInventory;
use App\Http\Requests\PayOrderRequest;
use App\Models\MenuAddOn;

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

            $orders = $orders->where(function ($query) use ($request) {
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
            $items = OrderItem::where('order_id', $order->order_id)->with('addons', 'menu')->get();

            $InventoryService = new InventoryService;
            if ($order->confirmed) {
                $inventoriesUsed = $InventoryService->getConfirmedInventoriesUsedByOrder($items);

            } else {
                $inventoriesUsed = $InventoryService->getInventoriesUsedByOrder($items);
            }

            $menu_ids = $items->pluck('menu_id');
            $_addons = MenuAddOn::whereIn('menu_id', $menu_ids)->get();

            $items->each(function ($item) use ($order, $_addons, $inventoriesUsed) {
                $item->errors = [];

                if ($order->pending) {
                    $menu = $item->menu;

                    if (!$menu) {
                        $item->errors = array_merge($item->errors, ['menu is unavailable for this item, please remove the item']);
                        return;
                    }

                    if ($item->inventory_id != null) {
                        if (array_key_exists($item->inventory_id, $inventoriesUsed)) {
                            $ivt = $inventoriesUsed[$item->inventory_id];

                            if ($ivt['running_stock'] < $ivt['total_used']) {
                                $item->errors = array_merge($item->errors, ["inventory item for (name: {$item->name}) does not have enough stock"]);
                                return;
                            }

                        } else {
                            $item->errors = array_merge($item->errors, ['inventory is unavailable for this item, please remove the item']);
                            return;
                        }
                    }

                    if (isset($item->data['has_addons']) && $item->data['has_addons'] == 1) {
                        $is_dinein = isset($item->data['is_dinein']) && $item->data['is_dinein'] == 1 ? true : false;
                        $addons = $_addons->where('menu_id', $item->menu_id)->where('is_dinein', $is_dinein);

                        if (count($addons) > 0) {
                            $addons->each(function ($addon) use (&$item, $inventoriesUsed) {
                                if (array_key_exists($addon->inventory_id, $inventoriesUsed)) {
                                    $addOnIvt = $inventoriesUsed[$addon->inventory_id];

                                    if ($addOnIvt['running_stock'] < $addOnIvt['total_used']) {
                                        $item->errors = array_merge($item->errors, ["addon item (name: {$addon->inventory->name}) does not have enough stock"]);
                                        return;
                                    }

                                } else {
                                    $item->errors = array_merge($item->errors, ['inventory is unavailable for an addon item, please delete the item or disable addon']);
                                    return;
                                }

                            });
                        }
                    }
                }

            });

            return view('orders.order_summary',compact('order', 'items','accounts', 'inventoriesUsed'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function printSummary(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->with('items')->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->with(['items' => function ($query) {
                $query->where('status', '!=', 'void');
            }])->first();

        }

        if ($order) {
            return view('orders.sections.summary_print',compact('order'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function printKitchenSummary(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->with(['items' => function ($query) {
                $query->with('addons');
                $query->where('status', '!=', 'void');
                $query->where('from', 'kitchen');
            }])->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->with(['items' => function ($query) {
                $query->with('addons');
                $query->where('status', '!=', 'void');
                $query->where('from', 'kitchen');
            }])->first();

        }

        if ($order) {
            return view('orders.sections.summary_kitchen_print', compact('order'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function printProductionSummary(Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->with(['items' => function ($query) {
                $query->where('status', '!=', 'void');
                $query->where('from', 'storage');
            }])->first();;
        } else {
            $order = Order::where('order_id', $request->order_id)->with(['items' => function ($query) {
                $query->where('status', '!=', 'void');
                $query->where('from', 'storage');
            }])->first();
        }

        if ($order) {
            return view('orders.sections.summary_production_print',compact('order'));
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

        if ($order) {
            if ($order->paid) {
                return redirect()->back()->with('error', 'Cannot add item for paid orders.');
            }

            $menus = Menu::with('category')->where(function ($q1) use ($order) {
                $q1->doesntHave('inventory');
                $q1->where('branch_id', $order->branch_id);
            })->orWhereHas('inventory', function ($q2) use ($order) {
                $q2->where('stock', '>', 0);
                $q2->where('branch_id', $order->branch_id);
            })->get();

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
                $addItem = $orderService->addItem($order, $request->menuitem, $request->quantity, $request->type, $request->grind_type, (bool) $request->isdinein, $request->has('applyAddon'));

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

        if (!$order->confirmed) {
            return redirect()->back();
        }

        $order_id = $request->order_id;
        $order_items = OrderItem::where('order_id', $order->order_id)->with('addons', 'menu')->get();

        $InventoryService = new InventoryService;
        $inventoriesUsed = $InventoryService->getConfirmedInventoriesUsedByOrder($order_items);

        return view('orders.sections.show_order_items', compact('order', 'order_items', 'order_id'));
    }

    public function showEditOrderItems (Request $request)
    {
        if (auth()->user()->branch_id) {
            $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $request->order_id)->first();
        } else {
            $order = Order::where('order_id', $request->order_id)->first();
        }

        $order_items = OrderItem::where('order_id', $order->order_id)->with('addons','menu')->get();

        $InventoryService = new InventoryService;

        if ($order->confirmed) {
            $inventoriesUsed = $InventoryService->getConfirmedInventoriesUsedByOrder($order_items);

        } else {
            $inventoriesUsed = $InventoryService->getInventoriesUsedByOrder($order_items);
        }

        $menu_ids = $order_items->pluck('menu_id');
        $_addons = MenuAddOn::whereIn('menu_id', $menu_ids)->get();

        $order_items->each(function ($item) use ($order, $_addons, $inventoriesUsed) {
            $item->errors = [];

            if ($order->pending) {
                $menu = $item->menu;

                if (!$menu) {
                    $item->errors = array_merge($item->errors, ['menu is unavailable for this item please remove the item']);
                    return;
                }
                if ($item->inventory_id != null) {
                    if (array_key_exists($item->inventory_id, $inventoriesUsed)) {
                        $ivt = $inventoriesUsed[$item->inventory_id];

                        if ($ivt['running_stock'] < $ivt['total_used']) {
                            $item->errors = array_merge($item->errors, ["inventory item for (name: {$item->name}) does not have enough stock"]);
                            return;
                        }

                    } else {
                        $item->errors = array_merge($item->errors, ['inventory is unavailable for this item please remove the item']);
                        return;
                    }
                }

                if (isset($item->data['has_addons']) && $item->data['has_addons'] == 1) {
                    $is_dinein = isset($item->data['is_dinein']) && $item->data['is_dinein'] == 1 ? true : false;
                    $addons = $_addons->where('menu_id', $item->menu_id)->where('is_dinein', $is_dinein);

                    if (count($addons) > 0) {
                        $addons->each(function ($addon) use (&$item, $inventoriesUsed) {
                            if (array_key_exists($addon->inventory_id, $inventoriesUsed)) {
                                $addOnIvt = $inventoriesUsed[$addon->inventory_id];

                                if ($addOnIvt['running_stock'] < $addOnIvt['total_used']) {
                                    $item->errors = array_merge($item->errors, ["addon item (name: {$addon->inventory->name}) does not have enough stock"]);
                                    return;
                                }

                            } else {
                                $item->errors = array_merge($item->errors, ['inventory is unavailable for an addon item']);
                                return;
                            }

                        });
                    }
                }
            }
        });

        return view('orders.sections.edit_items', compact('order', 'order_items', 'inventoriesUsed'));
    }

    public function updateOrderItems (Request $request, OrderService $orderService)
    {
        $request->validate([
            'quantity' => 'nullable|integer'
        ]);
        $item = OrderItem::where('id', $request->item_id)->first();

        if ($item) {
            if (auth()->user()->branch_id) {
                $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $item->order_id)->with('items')->first();
            } else {
                $order = Order::where('order_id', $item->order_id)->with('items')->first();
            }

            if ($order) {
                if ($order->confirmed) {
                    return redirect()->back()->with('error', 'Order is confirmed, cannot change order.');
                }

                try {
                    $response = $orderService->updateItem($order, $item, $request->quantity, $request->grind_type, (bool) $request->isdinein, $request->has('applyAddon'), $request->note);

                    return redirect()->back()->with('success', "Order Item  (name: $item->name) is updated successfully.");
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

    public function voidOrderItem (Request $request, OrderService $orderService)
    {
        $order_item = OrderItem::where('id', $request->id)->first();

        if ($order_item) {
            if (auth()->user()->branch_id) {
                $order = Order::where('branch_id', auth()->user()->branch_id)->where('order_id', $order_item->order_id)->first();
            } else {
                $order = Order::where('order_id', $order_item->order_id)->first();
            }

            if ($order) {
                try {
                    $response = $orderService->voidItem($order_item, $order);

                    return redirect()->back()->with('success', 'Order item is removed successfully.');
                } catch (\Exception $exception) {
                    //throw $th;
                    return redirect()->back()->with('error', $exception->getMessage());
                }

                return redirect()->back()->with('success', 'Order item is voided successfully.');
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

    public function pay (PayOrderRequest $request, $id, OrderService $orderService)
    {
        $order = Order::where('order_id', $id)->first();
        if ($order) {
            if ($order->cancelled) {
                return redirect()->back()->with('error', 'Order is cancelled.');
            } elseif ($order->completed) {
                return redirect()->back()->with('success', 'Order is already completed.');
            }

            $amount_given = floatval($request->input_amt);
            // $confirmed_amount = $order->confirmed_amount + $amount_given;


            DB::beginTransaction();
            try {
                $subtotal = $orderService->getOrderSubtotal($order->order_id);
                $discount_type = $order->discount_type;
                $discount_unit = $order->discount_unit ?? 0;
                $fees = $order->fees ?? 0;
                $deposit = $order->deposil_bal ?? 0;
                $cash_given = $order->amount_given + $amount_given;

                $invoice = $orderService->calculateOrderInvoice($subtotal, $discount_type, $discount_unit, $fees, $deposit, $cash_given);

                if ($invoice['discount'] > ($invoice['subtotal'] + $invoice['fees'])) {
                    return back()->with('error', "Discount amount cannot be greater than the order total.");
                }

                $order->subtotal = round(floatval($subtotal), 2);
                $order->total_amount = $invoice['total_amount'];
                $order->discount_amount = $invoice['discount'];
                $order->fees = $invoice['fees'];
                $order->deposit_bal = $invoice['deposit_balance'];
                $order->confirmed_amount = $invoice['amount_given'];
                $order->remaining_bal = $invoice['remaining_balance'];
                $order->amount_given = $invoice['cashgiven'];
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

            $subtotal = $orderService->getOrderSubtotal($order->order_id);
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
                $order->fees = $invoice['fees'];
                $order->subtotal = round(floatval($subtotal), 2);
                $order->total_amount = $invoice['total_amount'];
                $order->discount_amount = $invoice['discount'];
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

            DB::beginTransaction();
            try {
                // Recheck stock of items
                $ord_items = OrderItem::where('order_id', $order->order_id)->with('addons', 'menu')->get();

                $InventoryService = new InventoryService;
                $inventoriesUsed = $InventoryService->getInventoriesUsedByOrder($ord_items);

                $menu_ids = $ord_items->pluck('menu_id');
                $_addons = MenuAddOn::whereIn('menu_id', $menu_ids)->with('inventory')->get();

                foreach ($ord_items as $ord_item) {
                    $menu = $ord_item->menu;

                    if (!$menu) {
                        return redirect()->back()->with('error', "Order Item (name: $ord_item->name) menu is unavailable, please remove the item.");
                    }

                    if ($ord_item->inventory_id != null) {

                        // Check if the order item inventory item exis in inventories used
                        if (array_key_exists($ord_item->inventory_id, $inventoriesUsed)) {
                            $ivt = $inventoriesUsed[$ord_item->inventory_id];

                            if ($ivt['running_stock'] < $ivt['total_used']) {
                                return redirect()->back()->with('error', "Order Item (name: $ord_item->name) does not have enough stock.");
                            }
                        } else {
                            return redirect()->back()->with('error', "Order Item (name: $ord_item->name) inventory is unavailable, please remove the item.");
                        }

                    }

                    if (isset($ord_item->data['has_addons']) && $ord_item->data['has_addons'] == 1) {
                        $is_dinein = isset($ord_item->data['is_dinein']) && $ord_item->data['is_dinein'] == 1 ? true : false;
                        $addons = $_addons->where('menu_id', $ord_item->menu_id)->where('is_dinein', $is_dinein);

                        if (count($addons) > 0) {
                            foreach($addons as $addon) {
                                if (array_key_exists($addon->inventory_id, $inventoriesUsed)) {
                                    $addOnIvt = $inventoriesUsed[$addon->inventory_id];
                                    if ($addOnIvt['running_stock'] < $addOnIvt['total_used']) {
                                        $label = $addon->inventory->name;
                                        return redirect()->back()->with('error', "Order Item Addon (name: $label) does not have enough stock.");

                                    }
                                } else {
                                    return redirect()->back()->with('error', "Order Item (name: $ord_item->name) addon inventory is unavailable. Please remove the item.");
                                }

                                $createAddOn = new AddonOrderItem;
                                $createAddOn->create([
                                    'order_id' => $ord_item->order_id,
                                    'order_item_id' => $ord_item->order_item_id,
                                    'addon_id' => $addon->id,
                                    'inventory_id' => $addon->inventory->id,
                                    'inventory_name' => $addon->inventory->name,
                                    'inventory_code'=> $addon->inventory->inventory_code,
                                    'menu_id' => $addon->menu_id,
                                    'unit' => 1,
                                    'unit_label' => $addon->inventory->unit,
                                    'qty' => $addon->qty * $ord_item->qty,
                                    'is_dinein' => $addon->is_dinein
                                ]);
                            }
                        }
                    }

                    $ord_item->status = 'ordered';
                    $ord_item->save();
                }

                $recordedItems = [];
                $_usedIvt_ids = array_keys($inventoriesUsed);
                $_usedIvts = BranchMenuInventory::whereIn('id', $_usedIvt_ids)->with('branch')->get();

                foreach ($inventoriesUsed as $usedIvt) {
                    $_usedIvt = $_usedIvts->where('id', $usedIvt['inventory_id'])->first();
                    $deduct_inventory = $orderService->deductQtyToInventory($_usedIvt, 1, $usedIvt['total_used']);

                    // Deduct to inventory for order items
                    if ($deduct_inventory['status'] == 'fail') {
                        return redirect()->back()->with('error', "Order Item $ord_item->name does not have enough stock.");
                    }

                    $recordedItems = [
                        'branch_id' => $_usedIvt->branch_id,
                        'branch_name' => $_usedIvt->branch->name,
                        'inventory_id' => $usedIvt['inventory_id'],
                        'inventory_code' => $usedIvt['inventory_code'],
                        'name' => $usedIvt['name'],
                        'unit_label' => $_usedIvt->unit,
                        'total_qty' => -$usedIvt['total_used'],
                        'stock_before' => $usedIvt['running_stock'],
                        'stock_after' => floatval( $usedIvt['running_stock'] - $usedIvt['total_used'])
                    ];

                    InventoryLog::create([
                        'title' => 'Order Confirmation',
                        'data' => [
                            'module' => 'order',
                            'section' => 'confirm-order',
                            'order_id' => $order->order_id,
                            'inventory' => $recordedItems
                        ]
                    ]);
                }

                // Tag order as confirmed
                $order->pending = false;
                $order->confirmed = true;
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

                return redirect()->back()->with('error', "Something went wrong please recheck order.");
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

            // OrderItem::where('order_id', $order->order_id)
            //     ->update([
            //         'kitchen_cleared' => true,
            //         'dispatcher_cleared' => true,
            //         'production_cleared' => true
            //     ]);

            return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully cancelled.');
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function complete (Request $request, $id, OrderService $orderService)
    {
        $order = Order::with('items')->where('order_id', $id)->first();

        if ($order) {
            if ($order->cancelled) {
                return redirect()->route('order.list')->with('error', 'Cannot complete cancelled orders.');
            }

            if ($order->completed) {
                return redirect()->route('order.list')->with('error', 'Order is already completed.');
            }

            $acccount = BankAccount::where('id', $request->account)->first();

            if (!$acccount) {
                return redirect()->back()->with('error', 'Failed to complete order. Bank account does not exist.');
            }

            $subtotal = $orderService->getOrderSubtotal($order->order_id);
            $discount_type = $order->discount_type;
            $discount_unit = $order->discount_unit ?? 0;
            $fees = $order->fees ?? 0;
            $deposit = $order->deposil_bal ?? 0;
            $cash_given = $order->amount_given;

            $invoice = $orderService->calculateOrderInvoice($subtotal, $discount_type, $discount_unit, $fees, $deposit, $cash_given);

            if ($invoice['discount'] > ($invoice['subtotal'] + $invoice['fees'])) {
                return back()->with('error', "Discount amount cannot be greater than the order total.");
            }


            $order->subtotal = round(floatval($subtotal), 2);
            $order->total_amount = $invoice['total_amount'];
            $order->discount_amount = $invoice['discount'];
            $order->fees = $invoice['fees'];
            $order->deposit_bal = $invoice['deposit_balance'];
            $order->confirmed_amount = $invoice['amount_given'];
            $order->remaining_bal = $invoice['remaining_balance'];
            $order->amount_given = $invoice['cashgiven'];
            $order->pending = false;
            $order->completed = true;
            $order->cancelled = false;
            $order->save();

            // OrderItem::where('order_id', $order->order_id)
            //     ->update([
            //         'kitchen_cleared' => true,
            //         'dispatcher_cleared' => true,
            //         'production_cleared' => true
            //     ]);


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
                        'action' => 'Order Completion',
                        'amount' => $order->confirmed_amount,
                        'running_bal' => $new_bal,
                        'prev_bal' => $prev_bal
                    ]);
                }
            }


            return redirect()->route('order.show_summary', $order->order_id)->with('success', 'Order is successfully completed.');
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');

    }

    public function print (Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->with(['items' => function ($query) {
            $query->where('status', '!=', 'void');
        }])->first();

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
