<div>
    <form id="add-order-item-form" action="{{ route('order.add_item', $order->order_id) }}" method="post" autocomplete="off">
        @csrf
        <label class="block mb-4 text-sm" wire:ignore>
            <span class="text-gray-700 dark:text-gray-400">Menu Item</span>
            <select wire:model="menuid" id="item-select" class="styled-input" name="menuitem" required>
                <option value="" selected>Select menu item</option>
                @foreach ($menus as $item)
                    <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">{{ $item->name }} ({{ $item->inventory->branch->name }}) ({{  $item->inventory->stock }} left)</option>
                @endforeach
            </select>
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

        @if (isset($menuitem->is_beans))
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
            <input class="styled-input" name="quantity" type="number" placeholder="Enter Quantity" min="1" required>
        </label>

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
