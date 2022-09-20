<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\MenuCategory;
use App\Models\MenuInventory;
use App\Models\OrderDiscount;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PayOrderRequest;
use App\Models\AddonOrderItem;
use App\Models\BankTransaction;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\MenuAddOn;
use App\Services\AddonService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function showTakeOrder()
    {

        $categories = MenuCategory::with(['menus' => function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('stock', '>', 0);
            });
        }])->whereHas('menus', function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('stock', '>', 0);
            });
        });

        $categories = $categories->orderBy('name', 'ASC')->get()->toJson();

        return view('orders.take_order', compact('categories'));
    }

    public function showAddCart(Request $request)
    {
        $menu = Menu::whereHas('inventory', function ($q) {
            $q->where('stock', '>', 0);
        });
        $categories = MenuCategory::orderBy('name')->get();

        if ($request->except(['page'])) {
            $menu=$menu->where(function ($query) use ($request) {
                if ($request->menu !== null) {
                    $query->where('name', 'LIKE', '%' . $request->menu . '%');
                }
                if ($request->category !== null) {
                    $query->where('category_id', 'LIKE', '%' . $request->category . '%');
                }
            });
        }

        $menu = $menu->orderBy('name')->paginate(20);

        return view('orders.add_cart', compact('menu', 'categories'));
    }

    public function addCart(Request $request)
    {
        $item = Menu::where('id', $request->item_id)->first();
        $type = $request->type;
        $product_price = 0;
        if ($item) {
            $request->validate([
                'type' => 'required',
                'qty' => 'required|numeric|min:1'
            ]);

            // Check if there is enough stock
            $product_unit = $item->units;
            $quantity = $request->qty * $product_unit;
            $item_stock = $item->inventory->stock;
            if ($quantity > $item_stock) {
                return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have enough stock.");
            }

            // Order product price according to type
            if ($type == 'wholesale') {
                if ($item->wholesale_price == null && $item->wholesale_price == 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a wholesale price.");
                }
                $product_price = $item->wholesale_price;
            } else if ($type == 'regular') {
                if ($item->reg_price == null && $item->reg_price == 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a regular price.");
                }
                $product_price = $item->reg_price;
            } else if ($type == 'retail') {
                if ($item->retail_price == null && $item->retail_price == 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a retail price.");
                }
                $product_price = $item->retail_price;
            } else if ($type == 'rebranding') {
                if ($item->rebranding_price == null && $item->rebranding_price == 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a rebranding price.");
                }
                $product_price = $item->rebranding_price;
            } else if ($type == 'distributor') {
                if ($item->distributor_price == null && $item->distributor_price == 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a distributor price.");
                }
                $product_price = $item->distributor_price;
            } else {
                return redirect()->route('order.show_add_cart')->with('error', "Error encountered please inform Administrator.");
            }

            // Calculate total price
            $total = $product_price * $request->qty;

            // Check if the item is already in the cart
            $current_cart = Cart::where('admin_id', auth()->user()->id)
                ->where('type', $request->type)
                ->where('menu_id', $item->id)
                ->first();

            // if exist append the qty else create new item
            if ($current_cart) {
                return back()->with('warning', 'Item (name: '. $item->name .') is already in the cart.');
            } else {
                // Validate Add-on
                $AddonService = new AddonService;
                $response = $AddonService->validateAddon($request->cartAddon);

                if (isset($response) && $response['status'] == 'fail') {
                    return back()->with('error', $response['message']);
                }

                //Save the item to the cart
                Cart::create([
                    'admin_id' => auth()->user()->id,
                    'menu_id' => $item->id,
                    'inventory_id' => $item->inventory->id,
                    'name' => $item->name,
                    'type' => $type,
                    'units' => $item->units,
                    'price' => $product_price,
                    'qty' => $request->qty,
                    'total' => $total,
                    'data' => $response['data'] ?? []
                ]);
            }
            return back()->with('success', 'Item (name: '. $item->name .') has been successfully added.');
        }
        return redirect()->route('order.show_add_cart')->with('error', 'Item does not exist.');
    }

    public function viewCart()
    {
        $cart_items = Cart::where('admin_id', auth()->user()->id)->get();
        $discounts = OrderDiscount::where('active', 1)->get();
        $cart_subtotal = auth()->user()->cartItems->sum('total');
        $customers = Customer::all();

        // Check and tag the item if it is not available
        foreach ($cart_items as $item) {
            $item['available'] = true;

            // If the menu item does not exist tag available as false
            $menu_item = Menu::where('id', $item->menu_id)->first();
            if (!$menu_item) {
                $item['available'] = false;
            }
        }

        // unavailble item checker
        $unavailable_items = Cart::where('admin_id', auth()->user()->id)->doesnthave('menu')->count();

        return view('orders.sections.view_cart', compact(
            'cart_items',
            'unavailable_items',
            'discounts',
            'cart_subtotal',
            'customers'
        ));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'qty' => 'required|numeric|min:1',
            'note' => 'nullable|min:5|max:200'
        ]);

        $citem = Cart::where('id', $request->cart_id)->first();

        if ($citem) {
            // Check if the cart item belongs to admin
            if ($citem->admin_id != auth()->user()->id) {
                return redirect()->route('order.show_cart')->with('error', 'Item does not exist. (1)');
            }

            // Check if the item is available or out of stock
            $product_item = Menu::where('id', $citem->menu_id)->first();
            if ($product_item) {
                $cart_units= $citem->units;
                $total_cart_units = $request->qty * $cart_units;
                $cur_stock = $product_item->inventory->stock;
                if ($cur_stock < $total_cart_units) {
                    return redirect()->route('order.show_cart')->with('error', 'Product item (name: ' . $citem->name . ') does not have enough stock.');
                }

                // Validate Add-on
                $AddonService = new AddonService;
                $response = $AddonService->validateAddon($request->cartAddon);

                if (isset($response) && $response['status'] == 'fail') {
                    return back()->with('error', $response['message']);
                }

                $citem->update([
                    'qty' => $request->qty,
                    'total' => floatval($citem->price*$request->qty),
                    'note' => $request->note,
                    'data' => $response['data'] ?? []
                ]);
                return back()->with('success', 'Item (name: '. $citem->name .') has been successfully updated.');
            }
            return redirect()->route('order.show_cart')->with('error', 'Menu item does not exist or has been removed. (2)');
        }
        return redirect()->route('order.show_cart')->with('error', 'Item does not exist.');
    }

    public function deleteCart (Request $request)
    {
        $cart_item = Cart::where('id', $request->id)->first();

        if ($cart_item) {
           // Check if the cart item belongs to admin
            if ($cart_item->admin_id != auth()->user()->id) {
                return redirect()->route('order.show_cart')->with('error', 'Item does not exist. (1)');
            }
            $cart_item->delete();
            return back()->with('success', 'Item (name: '. $cart_item->name .') has been removed.');
        }
        return redirect()->route('order.show_cart')->with('error', 'Item does not exist.');
    }

    public function generateOrder (Request $request, OrderService $orderService) {
        $cartModel = Cart::where('admin_id', auth()->user()->id);
        $cart_items = $cartModel->get();

        if (!$cart_items) {
            return redirect()->route('order.show_cart')->with('error', 'Cart is empty, Add items to continue.');
        }

        // Check if all items in the cart are available
        $unavailable_items = Cart::where('admin_id', auth()->user()->id)->doesnthave('menu')->count();

        if ($unavailable_items > 0) {
            return redirect()->route('order.show_cart')->with('error', 'A cart item is unavailable. Please remove or change the item to proceed.');
        }

        $request->validate([
            'order_type' => 'required|string',
        ]);

        if ($request->customer) {
            $customer = Customer::where('id', $request->customer)->first();

            if (!$customer) {
                return redirect()->route('order.show_cart')->with('error', 'Customer account chosen does not exist or has been removed.');
            }
        }

        // Check each item if there is enough stock
        foreach ($cart_items as $citem) {
            if (!isset($citem->menu->inventory)) {
                return redirect()->route('order.show_cart')->with('error', "Failed to validate Cart Item (name: $citem->name ).");
            }

            $orderService = new OrderService;
            $checkStockResponse = $orderService->checkItemStock($citem->menu, $citem->units, $citem->qty);

            if (isset($checkStockResponse)) {
                if ($checkStockResponse['status'] == 'fail') {
                    return redirect()->route('order.show_cart')->with('error', "Cart Item (name: $citem->name ) does not have enough stock.");
                }
            }

            // Validate Add-on
            $AddonService = new AddonService;
            $addOnResponse = $AddonService->validateAddon($citem->data);
            if (isset($addOnResponse)) {
                if ($addOnResponse['status'] == 'fail') {
                    return redirect()->route('order.show_cart')->with('error', $addOnResponse['message']);
                }
            }
        }

        // Calculate fees, discounts and total amount
        $cart_subtotal = auth()->user()->cartItems->sum('total');
        $discount_amt = 0;

        if ($request->discount) {
            $discount = OrderDiscount::where('id', $request->discount)
            ->where('active', 1)
            ->first();

            if (!$discount && $request->discount != 'custom') {
                return redirect()->route('order.show_cart')->with('error', "Discount selected is not available.");
            }

            if ($request->discount == 'custom') {
                $discount_amt = $request->custom_discount ?? 0;
                $discount_label = "$request->discount";
            } else {
                if ($discount->type == 'percentage') {
                    // calculate percentage base on the subtotal of cart
                    $percentage = $discount->amount/100;
                    $discount_amt = $cart_subtotal * $percentage;
                    $discount_label = "$discount->type";
                } else {
                    $discount_amt = $discount->amount;
                    $discount_label = "$discount->type";
                }
            }
        }

        $fees = $request->fees >= 0 ? $request->fees : 0;
        $total_cart = $cart_subtotal + $fees;

        if ($discount_amt > $total_cart) {
            return redirect()->route('order.show_cart')->with('error', "Discount amount cannot be greater than the order total.");
        }


        // Calculate remaining balance
        $deposit_bal = $request->deposit ?? 0;

        $discount_type = isset($discount->type) ? $discount->type : null;
        $discount_unit = isset($discount->amount) ? $discount->amount : null;

        $orderService = new OrderService;
        $orderInvoice = $orderService->calculateOrderInvoice($cart_subtotal, $discount_type, $discount_unit, $fees, $deposit_bal, 0);

        DB::beginTransaction();
        try {
            $orderId = Order::generateUniqueId();

            // Save order
            $order = new Order;
            $order->order_id = $orderId;
            $order->customer_id =isset($customer->id) ? $customer->id : '';
            $order->customer_name = isset( $customer->name) ? $customer->name : '';
            $order->server_name = auth()->user()->name;
            $order->subtotal = $orderInvoice['subtotal'];
            $order->discount_amount = $orderInvoice['discount'];
            $order->fees = $fees;
            $order->deposit_bal = 0;
            $order->remaining_bal = $orderInvoice['remaining_balance'];
            $order->table = $request->tables;
            $order->total_amount = round(floatval($orderInvoice['total_amount']), 2);
            $order->discount_type = $discount_label ?? null;
            $order->discount_unit = $discount->amount ?? null;
            $order->order_type = $request->order_type;
            $order->delivery_method = $request->delivery_method;
            $order->pending = true;
            $order->save();

            // Save order items
            $save_items = [];
            $addon_item = [];

            foreach($cart_items as $citem) {

                // // Deduct to inventory for cart items
                // $deduct_inventory = $orderService->deductQtyToInventory($citem->menu->inventory, $citem->units, $citem->qty);

                // if ($deduct_inventory['status'] == 'fail') {
                //     return redirect()->back()->with('error', "Failed to generate order. Item $citem->name does not have enough stock.");
                // }

                // foreach ($citem->data as $addon) {
                //     $addonModel = MenuAddOn::where('id', $addon['addon_id'])->first();
                //     $deduct_addon = $orderService->deductQtyToInventory($addonModel->inventory, 1, $addon['qty']);

                //     if ($deduct_addon['status'] == 'fail') {
                //         return redirect()->back()->with('error', "Failed to generate order. Add-on Item $addon->name does not have enough stock.");
                //     }
                // }
                $orderItemId = OrderItem::generateUniqueId();

                $save_items[] = [
                    'order_id' => $order->order_id,
                    'order_item_id' => $orderItemId,
                    'menu_id' => $citem->menu_id,
                    'inventory_id' => $citem->inventory_id,
                    'inventory_name' => $citem->menu->inventory->name,
                    'name' => $citem->name,
                    'from' => $citem->menu->category->from,
                    'price' => $citem->price,
                    'type' => $citem->type,
                    'unit_label' => $citem->menu->inventory->unit,
                    'units' => $citem->units,
                    'qty' => $citem->qty,
                    'data' => json_encode($citem->data),
                    'total_amount' => round(floatval($citem->price*$citem->qty), 2),
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                if (isset($citem->data)) {
                    foreach ($citem->data as $addon) {
                        $addonModel = MenuAddOn::where('id', $addon['addon_id'])->first();

                        if (!$addonModel) {
                            return redirect()->route('order.show_cart')->with('error', "Addon Item (name: $addon->name ) does not exist.");
                        }

                        $addon_item[] = [
                            'order_id' => $order->order_id,
                            'order_item_id' => $orderItemId,
                            'addon_id' => $addon['addon_id'],
                            'inventory_id' => $addonModel->inventory_id,
                            'inventory_name' => $addonModel->inventory->name,
                            'name' => $addon['name'],
                            'qty' => $addon['qty'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            }

            DB::table('order_items')->insert($save_items);
            if (isset($citem->data)) {
                DB::table('addon_order_items')->insert($addon_item);
            }
            $cartModel->delete();

            DB::commit();
            return redirect()->route('order.show_cart')->with('success', 'Order ' . $order->order_id . ' is sucessfully created. Order is now being prepared.');

        } catch (\Exception $exception) {
            //catch $exception;
            DB::rollBack();

            ErrorLog::create([
                'location' => 'OrderController.generateOrder',
                'message' => $exception->getMessage()
            ]);

            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function showOrders(Request $request)
    {
        $orders = Order::where('cancelled', '!=', 1);

        if ($request->except(['page'])) {

            $orders = Order::where(function ($query) use ($request) {
                if ($request->order_id) {
                    $query->where('order_id', 'LIKE', "%$request->order_id%");
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
        $order = Order::where('order_id', $request->order_id)->with('items')->first();

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
        $order = Order::where('order_id', $request->order_id)->with('items')->first();

        if ($order) {
            return view('orders.sections.summary_print',compact('order'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }
    public function showAddOrderItem(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->first();
        $menus = Menu::all();

        if ($order) {
            if ($order->paid) {
                return redirect()->back()->with('error', 'Cannot add item for paid orders.');
            }

            return view('orders.sections.add_item',compact('order','menus'));
        }
        return redirect()->route('order.list')->with('error', 'Order does not exist.');
    }

    public function addOrderItem(Request $request, $order_id,  OrderService $orderService)
    {
        $order = Order::where('order_id', $order_id)->first();
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

    public function showEditOrderItems (Request $request)
    {
        $order_id = $request->order_id;
        $order_items = OrderItem::where('order_id', $order_id)->get();

        return view('orders.sections.edit_items', compact('order_items', 'order_id'));
    }

    public function updateOrderItems (Request $request, OrderService $orderService)
    {
        $request->validate([
            'quantity' => 'nullable|integer'
        ]);

        $item = OrderItem::where('id', $request->item_id)->first();

        if ($item) {
            $order = Order::where('order_id', $item->order_id)->first();
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
            $order = Order::where('order_id', $order_item->order_id)->first();
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
        $order = Order::where('order_id', $id)->first();
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
        $order = Order::where('order_id', $id)->first();
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
                        }
                    }

                    if ($ord_item->from == 'storage') {
                        $ord_item->status = 'done';
                    } else {
                        $ord_item->status = 'ordered';
                    }
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

            $order->pending = false;
            $order->completed = false;
            $order->cancelled = true;
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
