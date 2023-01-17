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
            <span class="text-gray-700 dark:text-gray-400">Order Type</span>
            <select
                name="isdinein"
                class="styled-input"
            >
                <option value="" disabled>Select type</option>
                <option value="1" @if (isset($orderItem->data->is_dinein) && $orderItem->data->is_dinein) selected @endif>Dine-in</option>
                <option value="0" @if (isset($orderItem->data->is_dinein) && !$orderItem->data->is_dinein) selected @endif>Takeout</option>
            </select>
        </label>

        @if (isset($orderItem->data->is_beans) && $orderItem->data->is_beans)
            <label class="block mb-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Grind Type</span>
                <select
                    name="grind_type"
                    class="styled-input"
                >
                    <option value="">Select grind type</option>
                    <option value="coarse"  @if ($orderItem->data->grind_type == 'coarse') selected @endif>Coarse</option>
                    <option value="medcoarse" @if ($orderItem->data->grind_type == 'medcoarse') selected @endif>Medium-Coarse</option>
                    <option value="medium" @if ($orderItem->data->grind_type == 'medium') selected @endif>Medium</option>
                    <option value="medfine" @if ($orderItem->data->grind_type == 'medfine') selected @endif>Medium-Fine</option>
                    <option value="fine" @if ($orderItem->data->grind_type == 'fine') selected @endif>Fine</option>
                </select>
            </label>
        @endif

        <label class="block mb-4 text-sm">
            <span class="text-gray-700">Quantity</span>
            <input class="styled-input" name="quantity" type="number" placeholder="Enter Quantity" min="1" value="{{ $orderItem->qty ?? 1 }}" required>
        </label>
    </form>
</div>
