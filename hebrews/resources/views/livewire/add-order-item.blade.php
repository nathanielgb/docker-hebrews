<div>
    <form id="add-order-item-form" action="{{ route('order.add_item', $order->order_id) }}" method="post" autocomplete="off">
        @csrf
        <!-- <label class="block mb-4 text-sm" wire:ignore>
            <span class="text-gray-700 dark:text-gray-400">Menu Item</span>
            <select id="select-menu" class="menu-items" placeholder="Select menu..."></select>
        </label> -->
        {{-- <label class="block mb-4 text-sm" wire:ignore>
            <span class="text-gray-700 dark:text-gray-400">Menu Item</span>
            <select wire:model="menuid" id="item-select" class="styled-input" name="menuitem" required>
                <option value="" selected>Select menu item</option>
                @foreach ($menus as $item)
                    <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">
                        {{ $item->name }}
                        @if ($item->inventory)
                            ({{ $item->inventory->branch->name }})
                            ({{  $item->inventory->stock }} left)
                        @endif
                    </option>
                @endforeach
            </select>
        </label> --}}
        <label class="block mb-4 text-sm" wire:ignore>
            <span class="text-gray-700 dark:text-gray-400">Menu Item</span>
            <select id="select-menu" wire:model="menuid" name="menuitem" class="select-menus mt-1" placeholder="Select menu..."></select>
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
            <span class="text-gray-700">Product Type</span>
            <select class="styled-input" name="type" required>
                <option value="" selected disabled>Select type</option>
                @if (isset($menuitem->reg_price))
                    <option value="regular">Regular ({{ $menuitem->reg_price }})</option>
                @endif
                @if (isset($menuitem->wholesale_price))
                    <option value="wholesale">Wholesale ({{ $menuitem->wholesale_price }})</option>
                @endif
                @if (isset($menuitem->rebranding_price))
                    <option value="rebranding">Rebranding ({{ $menuitem->rebranding_price }})</option>
                @endif
                @if (isset($menuitem->retail_price))
                    <option value="retail">Retail ({{ $menuitem->retail_price }})</option>
                @endif
                @if (isset($menuitem->distributor_price))
                    <option value="distributor">Distributor ({{ $menuitem->distributor_price }})</option>
                @endif
            </select>
        </label>

        @if (isset($menuitem->is_beans) && $menuitem->is_beans == 1)
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
            <input class="styled-input" wire:model.lazy="orderQty" name="quantity" type="number" placeholder="Enter Quantity" min="1" required>
            @if (isset($menuitem->inventory))
            <p class="text-xs text-yellow-500">current stock: {{ $menuitem->inventory->stock }}</p>
        @endif
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

        <div class="flex justify-center space-x-4" style="margin-top: 75px;">
            <a
                href="{{ route('order.show_summary',$order->order_id) }}"
                class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                >
                <span>BACK</span>
            </a>
            <button
                class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                >
                SAVE
            </button>
        </div>
    </form>
</div>
