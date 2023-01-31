<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\MenuAddOn;
use App\Models\OrderItem;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\OrderDiscount;
use App\Services\CartService;
use App\Services\AddonService;
use App\Services\OrderService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMenuInventory;

class CartController extends Controller
{
    public function showAddCart(Request $request)
    {
        $menu = Menu::where(function ($q1) {
            $q1->doesntHave('inventory');

            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q1->where('branch_id', auth()->user()->branch_id);
            }
        })->orWhereHas('inventory', function ($q2) {
            $q2->where('stock', '>', 0);

            // Check branch of current user
            if (auth()->user()->branch_id) {
                $q2->where('branch_id', auth()->user()->branch_id);
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

        $menu = $menu->with('category','inventory')->orderBy('name')->paginate(20);

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


            if ($item->inventory) {
                // Check if there is enough stock
                $product_unit = $item->units;
                $quantity = $request->qty * $product_unit;
                $item_stock = $item->inventory->stock;
                if ($quantity > $item_stock) {
                    return redirect()->route('order.show_add_cart')->with('error', "Item (name: {$item->name}) does not have enough stock.");
                }
            }

            // Order product price according to type
            $product_price = $item->getPrice($request->type);
            if ($product_price == null) {
                return back()->with('error', "Selected  product type for Item (name: {$item->name}) is invalid.");

            }

            // Calculate total price
            // $total = $product_price * $request->qty;
            $is_dinein =  isset($request->isdinein) && $request->isdinein == 1 ? true : false;

            // Check if the item is already in the cart
            $current_cart = Cart::where('admin_id', auth()->user()->id)
                ->where('data->is_dinein', $is_dinein)
                ->where('type', $request->type)
                ->where('menu_id', $item->id)
                ->first();

            // if exist append the qty else create new item
            if ($current_cart) {
                return back()->with('warning', 'Item (name: '. $item->name .') is already in the cart.');
            }

            // Check if there are other products of different branch
            $productOtherBranchFlag = Cart::where('admin_id', auth()->user()->id)
                ->whereHas('menu', function ($q) use ($item) {
                    // Check branch of current user
                    $q->where('branch_id', '!=', $item->branch_id);
                })->count();

            if($productOtherBranchFlag > 0) {
                return redirect()->route('order.show_add_cart')->with('error', "Cannot add item (name: $item->name), cart can only have items from a single branch. Choose a different item or remove items in the cart.");
            }

            // Order type of item
            $data = [
                'is_dinein' => $is_dinein,
                'is_beans' => isset($item->is_beans) && $item->is_beans == 1 ? true : false,
                'grind_type' => isset($request->grind_type) ? $request->grind_type : null,
                'has_addons' => $request->has('applyAddon')
            ];


            //Save the item to the cart
            Cart::create([
                'admin_id' => auth()->user()->id,
                'menu_id' => $item->id,
                'inventory_id' => isset($item->inventory) ? $item->inventory->id : null,
                // 'name' => $item->name,
                'type' => $type,
                // 'units' => $item->units,
                // 'price' => $product_price,
                'qty' => $request->qty,
                // 'total' => $total,
                'data' => $data
            ]);

            return back()->with('success', 'Item (name: '. $item->name .') has been successfully added.');
        }
        return redirect()->route('order.show_add_cart')->with('error', 'Item does not exist');
    }

    public function viewCart()
    {
        $cart_items = Cart::with(['menu' => function ($query) {
          // Check branch of current user
          if (auth()->user()->branch_id) {
                $query->where('branch_id', auth()->user()->branch_id);
            }
        }])->where('admin_id', auth()->user()->id)->get();

        $discounts = OrderDiscount::where('active', 1)->get();
        $customers = Customer::all();



        // Validate if items with inventory has enough stock or available
        $cart_items = Cart::where('admin_id', auth()->user()->id)
            ->select(DB::raw('carts.*, carts.id AS cart_id, menus.units, carts.qty*menus.units AS total_stocks'))
            ->join('menus', 'menus.id', '=', 'carts.menu_id')
            ->groupBy('carts.id')
            ->orderBy('carts.id', 'asc')
            ->orderBy('carts.menu_id', 'asc')
            ->get();

        $InventoryService = new InventoryService;
        $cart_subtotal = 0;

        foreach($cart_items as $cart_item) {
            $cart_item->available = true;
            $cart_item['errors']= [];
            $menu = $cart_item->menu;

            if (!$menu) {
                $cart_item->available = false;
                $cart_item->errors = 'menu item does not exist.';
            } else {
                $price = $menu->getPrice($cart_item->type);

                // Tag as unavailable if price of type is null
                if (!isset($price)) {
                    $cart_item['errors'] = array_merge($cart_item['errors'], ['no menu price available']);
                }

                $cart_subtotal = $cart_subtotal + ($price * $cart_item->qty);

                // get product price base on type
                $cart_item['price'] = $price;
                $cart_item['total'] = number_format($price * $cart_item->qty, 2, '.', '');

                if ($menu->inventory) {
                    // Append cart item for validating inventory ivailability
                    $InventoryService->setItem($cart_item);
                }

                if (isset($cart_item->data['has_addons']) && $cart_item->data['has_addons'] == 1) {
                    $is_dinein = isset($cart_item->data['is_dinein']) && $cart_item->data['is_dinein'] == 1 ? true : false;
                    $_addons = $menu->getAddonItems($is_dinein);

                    if (count($_addons) > 0) {
                        $cart_qty = $cart_item->qty ?? 1;
                        $cart_id = $cart_item->id;

                        $modified_addons = $_addons->map(function ($addon) use ($InventoryService, $cart_id, $cart_qty) {
                            $addon->total_stocks = $addon->qty * $cart_qty;
                            $addon->cart_id = $cart_id;

                            $InventoryService->setItem($addon);
                        });
                    }
                }
            }

        }

        $invalidCartItemsIds = $InventoryService->invalidCartItems();

        $cart_items->whereIn('cart_id', $invalidCartItemsIds)->each(function ($cartItem) {
            $cartItem->errors = array_merge($cartItem->errors, ['inventory stock is insufficient. check cart quantity or add-ons']);

            return $cartItem;
        });

        $inventoriesUsed = $InventoryService->getInventoriesUsed();

        // unavailble item checker
        $unavailable_items = Cart::where('admin_id', auth()->user()->id)->doesnthave('menu')->count();

        if (auth()->user()->branch_id) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('orders.sections.view_cart', compact(
            'cart_items',
            'unavailable_items',
            'discounts',
            'cart_subtotal',
            'customers',
            'branches',
            'inventoriesUsed'
        ));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'qty' => 'required|numeric|min:1',
            'type' => 'required',
            'note' => 'nullable|min:5|max:200'
        ]);

        $citem = Cart::where('id', $request->cart_id)->first();

        if ($citem) {
            // Check if the cart item belongs to admin
            if ($citem->admin_id != auth()->user()->id) {
                $citem->delete();
                return redirect()->route('order.show_cart')->with('error', 'Cart item is invalid. Item was removed.');
            }

            // Check if cart item is available base on branch
            $product_item = Menu::where('id', $citem->menu_id)->where(function ($q) {
                // Check branch of current user
                if (auth()->user()->branch_id) {
                    $q->where('branch_id', auth()->user()->branch_id);
                }
            })->first();

            if ($product_item) {
                if ($product_item->inventory) {
                    $cart_units= $citem->menu->units;
                    $total_cart_units = $request->qty * $cart_units;
                    $cur_stock = $product_item->inventory->stock;
                    if ($cur_stock < $total_cart_units) {
                        return redirect()->route('order.show_cart')->with('error', 'Product item (name: ' . $citem->menu->name . ') does not have enough stock.');
                    }
                }

                // Dine in flag
                $is_dinein = isset($request->isdinein) && $request->isdinein == 1 ? true : false;

                if ($request->has('applyAddon')) {
                    // Validate Add-on
                    $AddonService = new AddonService;
                    $response = $AddonService->validateAddon($product_item, $is_dinein, $request->qty);

                    if (isset($response) && $response['status'] == 'fail') {
                        return back()->with('error', $response['message']);
                    }
                }

                // Order type of item
                $data = [
                    'is_dinein' => $is_dinein,
                    'is_beans' => isset($product_item->is_beans) && $product_item->is_beans == 1 ? true : false,
                    'grind_type' => isset($request->grind_type) ? $request->grind_type : null,
                    'has_addons' => $request->has('applyAddon')
                ];

                $citem->update([
                    'qty' => $request->qty,
                    'type' => $request->type,
                    'note' => $request->note,
                    'data' => $data
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
            return back()->with('success', 'Item has been removed.');
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
            'fees' => 'nullable|numeric|between:0,9999999',
        ]);

        $customer_id = null;
        $customer_name = '';

        if ($request->has('noAccount')) {
            $customer_name = $request->customer_name;
        } else {
            if ($request->customer) {
                $customer = Customer::where('id', $request->customer)->first();
                $customer_id = $customer->id;
                $customer_name = $customer->name;

                if (!$customer) {
                    return redirect()->route('order.show_cart')->with('error', 'Customer account chosen does not exist or has been removed.');
                }
            }
        }

        $cart_subtotal = 0;
        $temp_addons = collect();

        // Check each item if there is enough stock
        foreach ($cart_items as $citem) {
            if (!isset($citem->menu)) {
                return redirect()->route('order.show_cart')->with('error', "Failed to validate a cart item. Menu does not exist.");
            }

            // Check if the cart item is available according to branch
            if ($request->branch != $citem->menu->branch_id) {
                return redirect()->route('order.show_cart')->with('error', "Cart Item (name: {$citem->menu->name}) is not available for the branch.");
            }

            $price = $citem->menu->getPrice($citem->type);

            // Tag as unavailable if price of type is null
            if (!isset($price)) {
                return redirect()->route('order.show_cart')->with('error', "Cart Item (name: {$citem->menu->name}) does not have a valid price.");
            }

            $cart_subtotal = $cart_subtotal + ($price * $citem->qty);

            $citem['price'] = $price;
            $citem['total'] = number_format($price * $citem->qty, 2, '.', '');
            if (isset($citem->data['has_addons']) && $citem->data['has_addons'] == 1) {
                $is_dinein = isset($citem->data['is_dinein']) && $citem->data['is_dinein'] == 1 ? true : false;
                $_addons = $citem->menu->getAddonItems($is_dinein);

                if (count($_addons) > 0) {
                    $cart_qty = $citem->qty ?? 1;
                    $cart_id = $citem->id;
                    $modified_addons = $_addons->map(function ($addon) use ($cart_id, $cart_qty) {
                        $addon->total_stocks = $addon->qty * $cart_qty;
                        $addon->cart_id = $cart_id;
                        return $addon;
                    });

                    $temp_addons = $temp_addons->merge($modified_addons);
                }

            }
        }

        // Validate addon of array
        if (count($temp_addons) > 0) {
            $inventory_ids = array_unique($temp_addons->pluck('inventory_id')->toArray());
            $ivt = BranchMenuInventory::whereIn('id', $inventory_ids)->get();

            foreach($inventory_ids as $id) {
                $items = $temp_addons->where('inventory_id', $id);
                $overall_stocks = $items->sum('total_stocks');

                $ivt1 = $ivt->where('id', $id)->first();

                if ($ivt1->stock < $overall_stocks) {
                    return redirect()->route('order.show_cart')->with('error', "Add-on inventory item (name: {$ivt1->name}) does not have enough stock. Reduce quantity of a cart item.");
                }
            }
        }

        // Validate if items with inventory has enough stock after summing all menus with the same inventory id
        $total_per_items = Cart::where('admin_id', auth()->user()->id)
            ->whereHas('menu', function ($query) {
                $query->whereHas('inventory');
            })
            ->select(DB::raw('carts.menu_id, carts.inventory_id, carts.data, menus.units, (carts.qty) AS total_qty, (carts.qty)*menus.units AS total_stocks'))
            ->join('menus', 'menus.id', '=', 'carts.menu_id')
            ->groupBy('carts.id')
            ->orderBy('carts.id', 'asc')
            ->orderBy('carts.menu_id', 'asc')
            ->get();

        $inventory_ids = array_unique($total_per_items->pluck('inventory_id')->toArray());
        $ivt = BranchMenuInventory::whereIn('id', $inventory_ids)->get();

        foreach($inventory_ids as $id) {
            $items = $total_per_items->where('inventory_id', $id);
            $overall_stocks = $items->sum('total_stocks');

            $ivt1 = $ivt->where('id', $id)->first();

            if ($ivt1->stock < $overall_stocks) {
                return redirect()->route('order.show_cart')->with('error', "Inventory Item (name: {$ivt1->name}) does not have enough stock. Reduce quantity of a cart item.");
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

        $fees = $request->fees >= 0 && $request->fees != null  ? $request->fees : 0;
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
            $order->customer_id = $customer_id;
            $order->customer_name = $customer_name;
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
                $orderItemId = OrderItem::generateUniqueId();

                $save_items[] = [
                    'order_id' => $order->order_id,
                    'order_item_id' => $orderItemId,
                    'menu_id' => $citem->menu_id,
                    'inventory_id' => isset($citem->menu->inventory) ? $citem->menu->inventory->id : null,
                    'inventory_name' =>  isset($citem->menu->inventory) ? $citem->menu->inventory->name : null,
                    'inventory_code' =>  isset($citem->menu->inventory) ? $citem->menu->inventory->inventory_code : null,
                    'name' => $citem->menu->name,
                    'from' => $citem->menu->category->from,
                    'price' => $citem->price,
                    'type' => $citem->type,
                    'unit_label' =>  isset($citem->menu->inventory) ? $citem->menu->inventory->unit : null,
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
                    // foreach ($citem->data as $addon) {
                    //     $addonModel = MenuAddOn::where('id', $addon['addon_id'])->first();

                    //     if (!$addonModel) {
                    //         return redirect()->route('order.show_cart')->with('error', "Addon Item (name: $addon->name ) does not exist.");
                    //     }

                    //     $addon_item[] = [
                    //         'order_id' => $order->order_id,
                    //         'order_item_id' => $orderItemId,
                    //         'addon_id' => $addon['addon_id'],
                    //         'inventory_id' => $addonModel->inventory_id,
                    //         'inventory_name' => $addonModel->inventory->name,
                    //         'inventory_code' => $addonModel->inventory->inventory_code,
                    //         'name' => $addon['name'],
                    //         'qty' => $addon['qty'],
                    //         'created_at' => Carbon::now(),
                    //         'updated_at' => Carbon::now(),
                    //     ];
                    // }
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

            return redirect()->back()->with('error', 'Something went wrong. Double check order details and try again.');
        }
    }
}
