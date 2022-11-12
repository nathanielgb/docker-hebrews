<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('menu', () => ({
                    subCat: null,
                }))
            })

            document.addEventListener('alpine:init', () => {
                Alpine.store('menu', {
                    deleteMenuData: [],
                    updateMenuData: [],
                    categories: null,
                    subCategories: null,
                    subCat: null,
                    setCategories(cat) {
                        this.categories = cat
                    },
                    setSubCategories (sub) {
                        this.subCategories = sub
                    },
                })
            })

        </script>
    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
    </x-slot>

    <x-slot name="header">
        {{ __('Menu') }}
    </x-slot>

    @include('components.alert-message')
    <div x-data="menu">
        <div class="flex justify-between my-3">
            <div class="flex justify-start space-x-2">
                @if(auth()->user()->can('access', 'view-categories-action'))
                    <a
                        href="{{ route('menu.show_categories') }}"
                        class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span>CATEGORIES</span>
                </a>

                @endif

                @if(auth()->user()->can('access', 'view-inventory-action'))
                <a
                    href="{{ route('menu.view_inventory') }}"
                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span>INVENTORY</span>
                </a>
                @endif

            </div>

            <div class="flex space-x-2 jusify-center">
                <a
                    href="{{ route('menu.index') }}"
                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span><i class="fa-solid fa-list"></i> VIEW ALL</span>
                </a>
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    data-bs-toggle="modal"
                    data-bs-target="#searchMenuModal"
                    >
                    <i class="fa-solid fa-magnifying-glass"></i> SEARCH
                </button>
                @if(auth()->user()->can('access', 'add-menu-action'))
                    <button
                        type="button"
                        class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                        data-bs-toggle="modal"
                        data-bs-target="#addMenuModal"
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
                        <th class="px-4 py-3">Menu ID</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 text-center">No. of Unit</th>
                        <th class="px-4 py-3">Inventory</th>
                        <th class="px-4 py-3">Prices</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($menu as $item)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm text-s">
                                    {{ $item->id }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    {{ $item->units }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if (isset($item->inventory))
                                        <ul>
                                            <li>ID:
                                                <span class="font-bold">
                                                    {{ $item->inventory->id }}
                                                </span>
                                            </li>
                                            <li>name:
                                                <span class="font-bold">
                                                    {{ $item->inventory->name }}
                                                </span>
                                            </li>
                                            <li>stock:
                                                <span class="font-bold">
                                                    @if ($item->inventory->unit == 'pcs' || $item->inventory->unit == 'boxes')
                                                        {{ intval($item->inventory->stock) }}
                                                    @else
                                                        {{ $item->inventory->stock }}
                                                    @endif
                                                </span>
                                            </li>
                                            <li>unit:
                                                <span class="font-bold">
                                                    {{ $item->inventory->unit }}
                                                </span>
                                            </li>
                                        </ul>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <ul>
                                        <li>regular: <span class="font-bold">{{ number_format($item->reg_price ?? 0, 2) }}</span></li>
                                        <li>retail: <span class="font-bold">{{ number_format($item->retail_price ?? 0, 2) }}</span></li>
                                        <li>wholesale: <span class="font-bold">{{ number_format($item->wholesale_price ?? 0, 2) }}</span></li>
                                        <li>distributor: <span class="font-bold">{{ number_format($item->distributor_price ?? 0, 2) }}</span></li>
                                        <li>rebranding: <span class="font-bold">{{ number_format($item->rebranding_price ?? 0, 2) }}</span></li>
                                    </ul>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span>
                                        {{ $item->category->name }}
                                    </span> <br>
                                    <span class="italic">
                                        {{ $item->sub_category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(auth()->user()->can('access', 'update-menu-action'))
                                        <div class="flex items-center space-x-4 text-sm">
                                            <button
                                                class="flex btn-update-menu items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateMenuModal"
                                                data-inventory="{{ $item->inventory_id }}"
                                                @click="$store.menu.updateMenuData={{ json_encode($item) }}, $store.menu.setCategories({{ $categories }}) ,$store.menu.setSubCategories({{ json_encode( $item->category->sub) }})"
                                                >
                                                <span><i class="fa-solid fa-pen"></i> Update</span>
                                            </button>
                                    @endif
                                    @if(auth()->user()->can('access', 'delete-menu-action'))
                                            <button
                                                @click="$store.menu.deleteMenuData={{ json_encode([
                                                    'id' => $item->id,
                                                    'name' => $item->name,
                                                ]) }}"
                                                type="button"
                                                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                                aria-label="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteMenuModal"
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
            @if ($menu->hasPages())
                <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                    {{ $menu->withQueryString()->links() }}
                </div>
            @endif
        </div>
        @include('menu.modals.add_menu')
        @include('menu.modals.search_menu')
        @include('menu.modals.delete_menu')
        @include('menu.modals.update_menu')
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">


            new TomSelect("#select-inventory",{
                allowEmptyOption: false,
                create: false
            });


            var updateControl = new TomSelect('#select-update-inventory', {
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: [],
            });

            $(".btn-update-menu").click(function() {
                updateControl.clear();
                var inventories = @json($inventory_items);
                var inventory_id = $(this).data("inventory");

                inventories.forEach(inventory => {
                    updateControl.addOption({
                        id: inventory.id,
                        name: inventory.name
                    });

                    if (inventory.id == inventory_id) {
                        updateControl.addItem(inventory.id);
                    }
                });
            });

            $("#addCategory").change(function() {
                var selectedItem = $(this).val();
                var subdata = $('option:selected',this).data("sub");

                var $subCategoryInput = $("#addSubCategory");
                $subCategoryInput.empty(); // remove old options

                $.each(subdata, function(key, value) {
                    $subCategoryInput.append($("<option></option>")
                        .attr("value", value).text(value));
                });
            });
            $("#updateCategory").change(function() {
                var selectedItem = $(this).val();
                var subdata = $('option:selected',this).data("sub");
                Alpine.store('menu').subCategories = subdata;
            });

        </script>
    </x-slot>
</x-app-layout>
