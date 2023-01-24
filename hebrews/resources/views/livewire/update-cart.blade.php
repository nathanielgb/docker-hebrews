<div>
    <form id="update-cart-form" action="{{ route('order.update_cart') }}" method="post">
        @csrf
        @php
            $cart = json_decode($cart);
        @endphp
        <input type="hidden" name="cart_id" value="{{ $cart->id ?? null }}">
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Name</span>
            <input
            type="text"
            class="styled-input--readonly"
            value="{{ $cart->menu->name ?? null }}"
            aria-label="menu item name"
            readonly/>
        </label>

        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Order Type</span>
            <select
                wire:model="selectedDineIn"
                name="isdinein"
                class="styled-input"
            >
                <option value="" disabled>Select type</option>
                <option value="1">Dine-in</option>
                <option value="0">Takeout</option>
            </select>
        </label>

        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Product Type</span>
            <select
                name="type"
                class="styled-input"
            >
                <option value="" disabled>Select type</option>
                @if (isset($cart->menu->reg_price))
                    <option value="regular" @if ($cart->type == 'regular') selected @endif>Regular ({{ $cart->menu->reg_price }})</option>
                @endif
                @if (isset($cart->menu->wholesale_price))
                    <option value="wholesale" @if ($cart->type == 'wholesale') selected @endif>Wholesale ({{ $cart->menu->wholesale_price }})</option>
                @endif
                @if (isset($cart->menu->rebranding_price))
                    <option value="rebranding" @if ($cart->type == 'rebranding') selected @endif>Rebranding ({{ $cart->menu->rebranding_price }})</option>
                @endif
                @if (isset($cart->menu->retail_price))
                    <option value="retail" @if ($cart->type == 'retail') selected @endif>Retail ({{ $cart->menu->retail_price }})</option>
                @endif
                @if (isset($cart->menu->distributor_price))
                    <option value="distributor" @if ($cart->type == 'distributor') selected @endif>Distributor ({{ $cart->menu->distributor_price }})</option>
                @endif
            </select>
        </label>


        @if (isset($cart->menu->is_beans) && $cart->menu->is_beans)
            <label class="block mb-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Grind Type</span>
                <select
                    name="grind_type"
                    class="styled-input"
                >
                    <option value="">Select grind type</option>
                    <option value="coarse"  @if ($cart->data->grind_type == 'coarse') selected @endif>Coarse</option>
                    <option value="medcoarse" @if ($cart->data->grind_type == 'medcoarse') selected @endif>Medium-Coarse</option>
                    <option value="medium" @if ($cart->data->grind_type == 'medium') selected @endif>Medium</option>
                    <option value="medfine" @if ($cart->data->grind_type == 'medfine') selected @endif>Medium-Fine</option>
                    <option value="fine" @if ($cart->data->grind_type == 'fine') selected @endif>Fine</option>
                </select>
            </label>
        @endif

        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <div class="flex space-x-2 align-center">
                <input wire:model="orderQty" class="styled-input" name="qty" type="number" placeholder="Enter quantity" value="{{ $cart->qty ?? null }}">
            </div>
            @if (isset($cart->menu->inventory))
                <p class="text-xs text-yellow-500">current stock: {{ $cart->menu->inventory->stock }}</p>
            @endif
        </label>

        <label class="block my-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Note</span>
            <textarea
                name="note"
                class="styled-textarea"
                rows="3"
                placeholder="Enter some additional note (optional)."
            >{{ $cart->note ?? '' }}</textarea>
        </label>

        @if (count($addOns) > 0)
            <div class="flex flex-col">
                <span class="text-gray-700 dark:text-gray-400">Add-On Items</span>
                <div class="form-check">
                    <input wire:model="applyAddon" name="applyAddon" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="flexCheckChecked" checked>
                    <label class="inline-block text-gray-800 form-check-label" for="flexCheckChecked">
                        Apply Add-ons
                    </label>
                </div>
                @if ($applyAddon)
                    <div class="add-on-table overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                            <table class="min-w-full border text-center">
                                <thead class="border-b">
                                    <tr>
                                        <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 border-r">
                                            Item
                                        </th>
                                        <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 border-r">
                                            Status
                                        </th>
                                        <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4">
                                            Qty
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($addOns as $addOn)
                                        <tr class="border-b">
                                            <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r">
                                                {{isset($addOn->inventory) ?  $addOn->inventory->name: 'N/A' }}
                                            </td>
                                            @php
                                                $_orderQty = !empty($orderQty) ? $orderQty : 0;
                                                $total_qty = ($_orderQty * $addOn->qty);
                                            @endphp
                                            <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r">
                                                @if (isset($addOn->inventory))
                                                    @if ($addOn->inventory->stock < $total_qty)
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                            UNAVAILABLE
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                            AVAILABLE
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                        UNAVAILABLE
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap">
                                                {{ $total_qty }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </form>
</div>
