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
            <input class="styled-input" name="qty" type="number" min="1"  placeholder="Enter quantity" required>
        </label>

    </form>
</div>
