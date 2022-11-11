<div>
    <form id="add-order-item-form" action="{{ route('order.add_item', $order->order_id) }}" method="post" autocomplete="off">
        @csrf
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Menu Item</span>
            <select wire:model="menuid" id="item-select" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-green-400 focus:outline-none focus:shadow-outline-green dark:focus:shadow-outline-gray" name="menuitem" required>
                <option value="" selected>Select menu item</option>
                @foreach ($menus as $item)
                    <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Product Type</span>
            <select class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-green-400 focus:outline-none focus:shadow-outline-green dark:focus:shadow-outline-gray" name="type" required>
                <option value="" selected disabled>Select type</option>
                @if (isset($menuitem->reg_price))
                    <option value="regular">Regular</option>
                @endif
                @if (isset($menuitem->wholesale_price))
                    <option value="wholesale">Wholesale</option>
                @endif
                @if (isset($menuitem->rebranding_price))
                    <option value="rebranding">Rebranding</option>
                @endif
                @if (isset($menuitem->retail_price))
                    <option value="retail">Retail</option>
                @endif
                @if (isset($menuitem->distributor_price))
                    <option value="distributor">Distributor</option>
                @endif
            </select>
        </label>
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <input class="block w-full mt-1 text-sm focus:border-green-400 focus:outline-none focus:shadow-outline-green form-input" name="quantity" type="number" placeholder="Enter Quantity" min="1" required>
        </label>

        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Qty</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($orderItemAddons as $index => $orderItemAddon)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm">
                                    <select
                                        wire:model="orderItemAddons.{{ $index }}.addon_id"
                                        name="orderItemAddon[{{ $index }}][addon_id]"
                                        class="block w-full text-sm form-select focus:outline-none focus:shadow-outline-gray"
                                    >
                                        <option value="" selected disabled>Select Add-on</option>
                                        @foreach ($addons as $addon)
                                            <option value="{{ $addon->id }}">{{ $addon->name }} ({{ $addon->inventory->stock }} left)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-sm text-center" style="max-width: 100px;">
                                    <input
                                        wire:model="orderItemAddons.{{ $index }}.qty"
                                        class="block w-full text-sm focus:outline-none focus:shadow-outline-gray form-input"
                                        name="orderItemAddon[{{ $index }}][qty]"
                                        type="number"
                                        min="1"
                                        placeholder="1"
                                    >
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <button
                                        wire:click.prevent="removeAddon({{ $index }})"
                                        type="button"
                                        class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                    >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="px-2 py-2">
                                <button
                                    wire:click.prevent="addAddon"
                                    type="button"
                                    class="inline-block px-4 py-2 ml-1 text-xs font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-blue-600 rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg"
                                    >
                                    <i class="fa-solid fa-circle-plus"></i> ADD ADD-ON
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center space-x-4">
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
