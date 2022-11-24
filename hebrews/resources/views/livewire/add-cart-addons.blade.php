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
            {{-- <input class="block w-full m-0 mt-1 text-sm font-normal text-gray-700 transition ease-in-out bg-gray-100 border border-gray-300 border-solid rounded form-control bg-clip-padding focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" value="{{ $cart->name ?? null }}" disabled> --}}
        </label>
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Product Type</span>
            <select
                name="type"
                class="styled-input"
            >
                <option value="" selected disabled>Select type</option>
                @if (isset($cart->reg_price))
                    <option value="regular">Regular ({{ $cart->reg_price }})</option>
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
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <input class="styled-input" name="qty" type="number" min="1"  placeholder="1" required>
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
                        {{-- {{ dd($cartAddons) }} --}}
                        @foreach ($cartAddons as $index => $cartAddon)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm">
                                    <select
                                        wire:model="cartAddons.{{ $index }}.addon_id"
                                        name="cartAddon[{{ $index }}][addon_id]"
                                        class="styled-input"
                                    >
                                        <option value="" selected disabled>Select Add-on</option>
                                        @foreach ($addons as $addon)
                                            <option value="{{ $addon->id }}">{{ $addon->name }} ({{ $addon->inventory->branch->name }}) ({{ $addon->inventory->stock }} left)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-sm text-center" style="max-width: 100px;">
                                    <input
                                    wire:model="cartAddons.{{ $index }}.qty"
                                    class="styled-input"
                                    name="cartAddon[{{ $index }}][qty]"
                                    type="number"
                                    min="1"
                                    placeholder="1">
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
    </form>
</div>
