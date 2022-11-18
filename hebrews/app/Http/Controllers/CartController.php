<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\MenuCategory;
use App\Models\OrderDiscount;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\MenuAddOn;
use App\Services\AddonService;
use App\Services\OrderService;

class CartController extends Controller
{
    public function showAddCart(Request $request)
    {
        $menu = Menu::whereHas('inventory', function ($q) {
            $q->where('stock', '>', 0);

            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', auth()->user()->branch_id);
            }
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
                if ($item->wholesale_price == null) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a wholesale price.");
                }
                $product_price = $item->wholesale_price;
            } else if ($type == 'regular') {
                if ($item->reg_price == null) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a regular price.");
                }
                $product_price = $item->reg_price;
            } else if ($type == 'retail') {
                if ($item->retail_price == null) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a retail price.");
                }
                $product_price = $item->retail_price;
            } else if ($type == 'rebranding') {
                if ($item->rebranding_price == null) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a rebranding price.");
                }
                $product_price = $item->rebranding_price;
            } else if ($type == 'distributor') {
                if ($item->distributor_price == null) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have a distributor price.");
                }
                $product_price = $item->distributor_price;
            } else {
                return redirect()->route('order.show_add_cart')->with('error', "Error encountered please inform Administrator.");
            }

            // Calculate total price
            // $total = $product_price * $request->qty;

            // Check if the item is already in the cart
            $current_cart = Cart::where('admin_id', auth()->user()->id)
                ->where('type', $request->type)
                ->where('menu_id', $item->id)
                ->first();

            // if exist append the qty else create new item
            if ($current_cart) {
                return back()->with('warning', 'Item (name: '. $item->name .') is already in the cart.');
            } else {

                // Check if there are other products of different branch
                $productOtherBranchFlag = Cart::where('admin_id', auth()->user()->id)
                    ->whereHas('inventory', function ($q) use ($item) {
                        // Check branch of current user
                        $q->where('branch_id', '!=',$item->inventory->branch_id);

                    })->count();

                if($productOtherBranchFlag > 0) {
                    return redirect()->route('order.show_add_cart')->with('error', "Cannot add item (name: $item->name), cart can only have items from a single branch. Choose a different item or remove items in the cart.");
                }

                // Validate Add-on
                $AddonService = new AddonService;
                $response = $AddonService->validateAddon($request->cartAddon, $item);

                if (isset($response) && $response['status'] == 'fail') {
                    return back()->with('error', $response['message']);
                }

                //Save the item to the cart
                Cart::create([
                    'admin_id' => auth()->user()->id,
                    'menu_id' => $item->id,
                    'inventory_id' => $item->inventory->id,
                    // 'name' => $item->name,
                    'type' => $type,
                    // 'units' => $item->units,
                    // 'price' => $product_price,
                    'qty' => $request->qty,
                    // 'total' => $total,
                    'data' => $response['data'] ?? []
                ]);
            }
            return back()->with('success', 'Item (name: '. $item->name .') has been successfully added.');
        }
        return redirect()->route('order.show_add_cart')->with('error', 'Item does not exist.');
    }

    public function viewCart()
    {
        $cart_items = Cart::with('menu')->where('admin_id', auth()->user()->id)
            ->whereHas('inventory', function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            })->get();

        $discounts = OrderDiscount::where('active', 1)->get();

        $customers = Customer::all();

        $cart_subtotal = 0;

        // Check and tag the item if it is not available
        foreach ($cart_items as $item) {
            $item['available'] = true;

            // If the menu item does not exist tag available as false
            $menu_item = Menu::where('id', $item->menu_id)->first();
            // Tag as unavailable if no menu found
            if (!$menu_item) {
                $item['available'] = false;
            } else {
                $price = $menu_item->getPrice($item->type);

                // Tag as unavailable if price of type is null
                if (!isset($price)) {
                    $item['available'] = false;
                }

                $cart_subtotal = $cart_subtotal + ($price * $item->qty);

                // get product price base on type
                $item['price'] = $price;
                $item['total'] = number_format($price * $item->qty, 2, '.', '');

            }
        }


        // unavailble item checker
        $unavailable_items = Cart::where('admin_id', auth()->user()->id)->doesnthave('menu')->count();

        if (auth()->user()->branch_id) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        $addons = MenuAddOn::whereHas('inventory', function ($q) {
            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q->where('branch_id', auth()->user()->branch_id);
            }
        })->get();

        return view('orders.sections.view_cart', compact(
            'cart_items',
            'unavailable_items',
            'discounts',
            'cart_subtotal',
            'customers',
            'branches',
            'addons'
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
                $citem->delete();
                return redirect()->route('order.show_cart')->with('error', 'Cart item is invalid, Item was removed.');
            }

            // Check if cart item is available base on branch
            $product_item = Menu::where('id', $citem->menu_id)->whereHas('inventory', function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            })->first();

            if ($product_item) {
                $cart_units= $citem->menu->units;
                $total_cart_units = $request->qty * $cart_units;
                $cur_stock = $product_item->inventory->stock;
                if ($cur_stock < $total_cart_units) {
                    return redirect()->route('order.show_cart')->with('error', 'Product item (name: ' . $citem->name . ') does not have enough stock.');
                }

                // Validate Add-on
                $AddonService = new AddonService;
                $response = $AddonService->validateAddon($request->cartAddon, $product_item);

                if (isset($response) && $response['status'] == 'fail') {
                    return back()->with('error', $response['message']);
                }

                $citem->update([
                    'qty' => $request->qty,
                    'note' => $request->note,
                    'data' => $response['data'] ?? []
                ]);

                return back()->with('success', 'Item (name: '. $product_item->name .') has been successfully updated.');
            }
            return redirect()->route('order.show_cart')->with('error', 'Menu item does not exist or is not available.');
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

        $cart_subtotal = 0;
        // Check each item if there is enough stock
        foreach ($cart_items as $citem) {
            if (!isset($citem->menu->inventory)) {
                return redirect()->route('order.show_cart')->with('error', "Failed to validate a cart item. Menu or inventory does not exist.");
            }

            // Check if the cart item is available according to branch
            if ($request->branch != $citem->menu->inventory->branch_id) {
                return redirect()->route('order.show_cart')->with('error', "Cart Item (name: {$citem->menu->name}) is not available for the branch.");
            }

            $orderService = new OrderService;
            $checkStockResponse = $orderService->checkItemStock($citem->menu, $citem->menu->units, $citem->qty);

            if (isset($checkStockResponse)) {
                if ($checkStockResponse['status'] == 'fail') {
                    return redirect()->route('order.show_cart')->with('error', "Cart Item (name: {$citem->menu->name}) does not have enough stock.");
                }
            }

            $price = $citem->menu->getPrice($citem->type);

            // Tag as unavailable if price of type is null
            if (!isset($price)) {
                return redirect()->route('order.show_cart')->with('error', "Cart Item (name: {$citem->menu->name}) does not have a valid price.");
            }

            $cart_subtotal = $cart_subtotal + ($price * $citem->qty);


            $citem['price'] = $price;
            $citem['total'] = number_format($price * $citem->qty, 2, '.', '');

            // Validate Add-on
            $AddonService = new AddonService;
            $addOnResponse = $AddonService->validateAddon($citem->data, $citem->menu);
            if (isset($addOnResponse)) {
                if ($addOnResponse['status'] == 'fail') {
                    return redirect()->route('order.show_cart')->with('error', $addOnResponse['message']);
                }
            }
        }

        // Calculate fees, discounts and total amount
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

        $discount_type = isset($discount->type) ? $discount->type : '';
        $discount_unit = isset($discount->amount) ? $discount->amount : 0;

        $orderService = new OrderService;
        $orderInvoice = $orderService->calculateOrderInvoice($cart_subtotal, $discount_type, $discount_unit, $fees, $deposit_bal, 0);

        DB::beginTransaction();
        try {
            $orderId = Order::generateUniqueId();

            // Save order
            $order = new Order;
            $order->order_id = $orderId;
            $order->branch_id = $request->branch;
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
            $order->discount_type = $discount_type;
            $order->discount_unit = $discount_unit;
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
                    'inventory_code' => $citem->menu->inventory->inventory_code,
                    'name' => $citem->menu->name,
                    'from' => $citem->menu->category->from,
                    'price' => $citem->price,
                    'type' => $citem->type,
                    'unit_label' => $citem->menu->inventory->unit,
                    'units' => $citem->menu->units,
                    'qty' => $citem->qty,
                    'data' => json_encode($citem->data),
                    'total_amount' => $citem->total,
                    'status' => 'pending',
                    'note' => $citem->note,
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
                            'inventory_code' => $addonModel->inventory->inventory_code,
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
}
