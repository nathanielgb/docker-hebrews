<div>
    <form id="update-cart-form" action="{{ route('order.update_cart') }}" method="post">
        @csrf
        @php
            $cart = json_decode($cart);
        @endphp
        <input type="hidden" name="cart_id" value="{{ $cart->id ?? null }}">
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <div class="flex space-x-2 align-center">
                <input class="styled-input" name="qty" type="number" placeholder="Enter quantity" value="{{ $cart->qty ?? null }}">
            </div>
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
                                            <option value="{{ $addon['id'] }}">
                                                @if (isset($addon['inventory']))
                                                    {{ $addon['name'] }} ({{ $addon['inventory']['branch']['name'] }}) ({{ $addon['inventory']['stock'] }} left)
                                                @else
                                                    {{ $addon['name'] }}
                                                @endif
                                            </option>
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

        <label class="block my-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Note</span>
            <textarea
                name="note"
                class="styled-textarea"
                rows="3"
                placeholder="Enter some additional note (optional)."
            >{{ $cart->note ?? '' }}</textarea>
        </label>
    </form>
</div>
