<div>
    <form id="add-cart-form" method="post" action="{{ route('order.add_cart') }}">
        @csrf
        @php
            $cart = json_decode($cart);
        @endphp
        <input type="hidden" name="item_id" value="{{ $cart->id ?? null }}">
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Name</span>
            <input
            type="text"
            class="styled-input--readonly"
            value="{{ $cart->name ?? null }}"
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
                <option value="1" selected>Dine-in</option>
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
                @if (isset($cart->reg_price))
                    <option value="regular" selected>Regular ({{ $cart->reg_price }})</option>
                @endif
                @if (isset($cart->wholesale_price))
                    <option value="wholesale">Wholesale ({{ $cart->wholesale_price }})</option>
                @endif
                @if (isset($cart->rebranding_price))
                    <option value="rebranding">Rebranding ({{ $cart->rebranding_price }})</option>
                @endif
                @if (isset($cart->retail_price))
                    <option value="retail">Retail ({{ $cart->retail_price }})</option>
                @endif
                @if (isset($cart->distributor_price))
                    <option value="distributor">Distributor ({{ $cart->distributor_price }})</option>
                @endif
            </select>
        </label>

        @if (isset($cart->is_beans) && $cart->is_beans)
            <label class="block mb-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Grind Type</span>
                <select
                    name="grind_type"
                    class="styled-input"
                >
                    <option value="" selected>Select grind type</option>
                    <option value="coarse">Coarse</option>
                    <option value="medcoarse">Medium-Coarse</option>
                    <option value="medium">Medium</option>
                    <option value="medfine">Medium-Fine</option>
                    <option value="fine">Fine</option>
                </select>
            </label>
        @endif

        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <input wire:model.lazy="orderQty" class="styled-input" name="qty" type="number" min="1"  placeholder="Enter quantity" required>
        </label>
            <div class="flex flex-col">
                <span class="text-gray-700 dark:text-gray-400">Add-On Items</span>
                <div class="form-check">
                    <input wire:model="applyAddon" name="applyAddon" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="flexCheckChecked" checked>
                    <label class="inline-block text-gray-800 form-check-label" for="flexCheckChecked">
                        Apply Add-ons
                    </label>
                </div>
                @if ($applyAddon)
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                            <table class="min-w-full border text-center">
                                <thead class="border-b">
                                    <tr>
                                        <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 border-r">
                                            Item
                                        </th>
                                        <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4">
                                            Qty
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($addOns) > 0)
                                        @foreach ($addOns as $addOn)
                                            <tr class="border-b">
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r">
                                                    {{isset($addOn->inventory) ?  $addOn->inventory->name: 'N/A' }}
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $_orderQty = !empty($orderQty) ? $orderQty : 0;
                                                        $total_qty = $_orderQty * $addOn->qty;
                                                    @endphp
                                                    {{ $total_qty }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="border-b">
                                            <td colspan="2" class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r">
                                                No addons found.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
    </form>
</div>
