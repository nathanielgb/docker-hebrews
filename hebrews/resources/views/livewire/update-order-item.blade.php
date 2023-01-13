<div>
    <form id="edit-item-form" method="POST" action="{{ route('order.update_item') }}" autocomplete="off">
        @csrf
        @php
            $orderItem = json_decode($orderItem);
        @endphp
        <input type="hidden" name="item_id" value="{{ $orderItem->id ?? null }}">
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Name</span>
            <input
            type="text"
            class="styled-input--readonly"
            value="{{ $orderItem->name ?? null }}"
            aria-label="menu item name"
            readonly/>
        </label>
        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <input class="styled-input" name="quantity" type="number" placeholder="Enter Quantity" min="1" value="{{ $orderItem->qty ?? 1 }}" required>
        </label>
    </form>
</div>
