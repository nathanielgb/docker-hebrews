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
