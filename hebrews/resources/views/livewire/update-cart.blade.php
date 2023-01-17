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
                name="isdinein"
                class="styled-input"
            >
                <option value="" disabled>Select type</option>
                <option value="1" @if (isset($cart->data->is_dinein) && $cart->data->is_dinein) selected @endif>Dine-in</option>
                <option value="0" @if (isset($cart->data->is_dinein) && !$cart->data->is_dinein) selected @endif>Takeout</option>
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
                <input class="styled-input" name="qty" type="number" placeholder="Enter quantity" value="{{ $cart->qty ?? null }}">
            </div>
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
    </form>
</div>
