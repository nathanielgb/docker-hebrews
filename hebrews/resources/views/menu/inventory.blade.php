<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('inventory', {
                    deleteInventoryData: [],
                    updateInventoryData: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Inventory - Warehouse') }}
    </x-slot>

    @include('components.alert-message')

    <div class="inline-flex w-full mt-2 mb-4 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-center w-12 bg-yellow-400">
            <i class="text-lg text-white fa-solid fa-circle-exclamation"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-yellow-400">Warning</span>
                <p class="text-sm text-gray-600">Items in warehouse will <b>NOT APPEAR</b> in Menu. Transfer items to desired Branch Inventory to be able to add menu items.</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between my-3">
        <div>
            {{-- <a
                href="{{ route('menu.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a> --}}
        </div>

        <div class="flex space-x-2 jusify-center">
            <a
                href="{{ route('menu.view_inventory') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-list"></i> VIEW ALL</span>
            </a>

            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#searchInventoryModal"
                >
                <i class="fa-solid fa-magnifying-glass"></i> SEARCH
            </button>
            @if (auth()->user()->can('access', 'add-inventory-action'))
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    data-bs-toggle="modal"
                    data-bs-target="#addInventoryModal"
                    >
                    <i class="fa-solid fa-circle-plus"></i> ADD
                </button>
            @endif
        </div>

    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 text-center">ID</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Name</th>
                    {{-- <th class="px-4 py-3">Linked Products</th> --}}
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Modified by</th>
                    <th class="px-4 py-3">Last updated</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($inventory_items as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->inventory_code }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->category->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->name }}
                            </td>
                            {{-- <td class="px-4 py-3 text-sm">
                                <ul>
                                    <li>menu: <span class="font-bold">{{ count($item->products  ?? []) }}</span></li>
                                    <li>add-on: <span class="font-bold">{{ count($item->addons  ?? []) }}</span></li>
                                </ul>

                            </td> --}}
                            <td class="px-4 py-3 text-sm">
                                <ul>
                                    <li>current stock:
                                        <span class="font-bold">
                                            @if ($item->unit == 'pcs' || $item->unit == 'boxes')
                                                {{ intval($item->stock) }}
                                            @else
                                                {{ $item->stock }}
                                            @endif
                                        </span>
                                    </li>
                                    <li>previous stock:
                                        <span class="font-bold">
                                            @if ($item->unit == 'pcs' || $item->unit == 'boxes')
                                                {{ intval($item->previous_stock) }}
                                            @else
                                                {{ $item->previous_stock }}
                                            @endif
                                        </span>
                                    </li>
                                    <li>unit: <span class="font-bold">{{ $item->unit }}</span></li>
                                </ul>

                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->modified_by }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($item->updated_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (auth()->user()->can('access', 'manage-inventory-action'))
                                    <div class="flex justify-center space-x-4 text-sm">
                                        @if (auth()->user()->can('access', 'transfer-inventory-action'))
                                            <button
                                                class="flex items-center inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#transferInventoryModal"
                                                @click="$store.inventory.updateInventoryData={{ json_encode($item) }}"
                                                >
                                                <span><i class="fa-solid fa-right-left"></i> Transfer</span>
                                            </button>
                                        @endif
                                        <button
                                            class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateInventoryModal"
                                            @click="$store.inventory.updateInventoryData={{ json_encode($item) }}"
                                            >
                                            <span><i class="fa-solid fa-pen"></i> Update</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out" aria-label="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteInventoryModal"
                                            @click="$store.inventory.deleteInventoryData={{ json_encode([
                                                'id' => $item->id,
                                                'name' => $item->name,
                                            ]) }}"
                                            >
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </div>
                                @endif
                        </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="7" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($inventory_items->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $inventory_items->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('menu.modals.transfer_inventory')
    @include('menu.modals.update_inventory')
    @include('menu.modals.search_inventory')
    @include('menu.modals.add_inventory')
    @include('menu.modals.delete_inventory')


</x-app-layout>
