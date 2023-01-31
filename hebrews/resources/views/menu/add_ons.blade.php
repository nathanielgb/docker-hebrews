<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('data', {
                    delete: []
                })
            })
        </script>
    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
        <style>
            .select-inventories .optgroup-header {
                font-weight: 700;
                font-style: italic;
                opacity: 1;
                margin: 0 0 0 2px;
            }
        </style>
    </x-slot>

    <div class="flex justify-start">
        <div>
            <h2 class="my-3 text-2xl font-semibold text-gray-700">Menu (name: {{ $menu->name }}) - Add ons</h2>
        </div>
    </div>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div class="flex space-x-2 jusify-center">
            <a
                href="{{ route('menu.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>
        <div class="flex space-x-2 jusify-center">
            @if (auth()->user()->can('access', 'manage-menu-addons-action'))
            <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    data-bs-toggle="modal"
                    data-bs-target="#addMenuAddonsModal"
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
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Order Type</th>
                    <th class="px-4 py-3">Inventory</th>
                    <th class="px-4 py-3 text-center">Quantity</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($addons as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($item->is_dinein)
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                @else
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($item->inventory)
                                    <ul>
                                        <li>branch: <span class="font-bold">{{ $item->inventory->branch->name }}</span></li>
                                        <li>name: <span class="font-bold">{{ $item->inventory->name }}</span></li>
                                        <li>code: <span class="font-bold">{{ $item->inventory->inventory_code }}</span></li>
                                        <li>stock: <span class="font-bold">{{ $item->inventory->stock }}</span></li>
                                        <li>unit: <span class="font-bold">{{ $item->inventory->unit }}</span></li>
                                    </ul>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->qty }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (auth()->user()->can('access', 'manage-menu-addons-action'))
                                    <div class="flex items-center justify-center space-x-4 text-sm">
                                        {{-- <a
                                            href="{{ route('bank.account.transactions', $item->id) }}"
                                            class="flex items-center inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-500 hover:shadow-lg focus:bg-green-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-500 active:shadow-lg transition duration-150 ease-in-out"
                                            >
                                            <span><i class="fa-solid fa-eye"></i> View</span>
                                        </a> --}}

                                        <button
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            @click="$store.data.delete={{ json_encode([
                                                'id' => $item->id ?? '',
                                                'name' => $item->inventory->name ?? '',
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
                            <td colspan="5" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($addons->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $addons->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('menu.modals.add_addons')
    @include('menu.modals.delete_addons')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            var inventories = @json($inventory_items);
            var newCategories = [];

            var newInventories = inventories.map(element => {
                let category = {};
                category.value = element.category_id;
                category.label = element?.category?.name;
                category.from = element?.category?.from;
                newCategories.push(category);

                return {
                    class: element.category_id,
                    category: element?.category?.name,
                    value: element.id,
                    name: element.name
                };
            });

            new TomSelect('#select-inventory',{
                sortField: [{
                    field: "category",
                    direction: "asc",
                },{
                    field: "name",
                    direction: "asc",
                }],
                options: newInventories,
                optgroups: newCategories,
                optgroupField: 'class',
                labelField: 'name',
                searchField: ['name'],
                render: {
                    optgroup_header: function(data, escape) {
                        return '<div class="optgroup-header">' + escape(data.label) + '</div>';
                    }
                }
            });
        </script>
    </x-slot>
</x-app-layout>
