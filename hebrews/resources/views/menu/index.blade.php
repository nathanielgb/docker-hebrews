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
                @if(auth()->user()->can('access', 'import-menu-action'))
                    <a
                        href="{{ route('menu.import.view') }}"
                        class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span>IMPORT</span>
                </a>
                @endif

                {{-- @if(auth()->user()->can('access', 'view-inventory-action'))
                    <a
                        href="{{ route('menu.view_inventory') }}"
                        class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                        >
                        <span>INVENTORY</span>
                    </a>
                @endif --}}

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
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 text-center">No. of Unit</th>
                        <th class="px-4 py-3">Inventory</th>
                        <th class="px-4 py-3">Prices</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3 text-center">Is Beans</th>
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
                                    {{ $item->code }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $item->branch->name ?? '' }}
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
                                            <li>branch:
                                                <span class="font-bold">
                                                    {{ $item->inventory->branch->name }}
                                                </span>
                                            </li>
                                            <li>name:
                                                <span class="font-bold">
                                                    {{ $item->inventory->name }}
                                                </span>
                                            </li>
                                            <li>code:
                                                <span class="font-bold">
                                                    {{ $item->inventory->inventory_code }}
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
                                    @php
                                        $reg_price = isset($item->reg_price) ? number_format($item->reg_price, 2) : 'N/A';
                                        $retail_price = isset($item->retail_price) ? number_format($item->retail_price, 2) : 'N/A';
                                        $wholesale_price = isset($item->wholesale_price) ? number_format($item->wholesale_price, 2) : 'N/A';
                                        $distributor_price = isset($item->distributor_price) ? number_format($item->distributor_price, 2) : 'N/A';
                                        $rebranding_price = isset($item->rebranding_price) ? number_format($item->rebranding_price, 2) : 'N/A';
                                    @endphp
                                    <ul>
                                        <li>regular: <span class="font-bold">{{ $reg_price }}</span></li>
                                        <li>retail: <span class="font-bold">{{ $retail_price }}</span></li>
                                        <li>wholesale: <span class="font-bold">{{ $wholesale_price }}</span></li>
                                        <li>distributor: <span class="font-bold">{{ $distributor_price }}</span></li>
                                        <li>rebranding: <span class="font-bold">{{ $rebranding_price}}</span></li>
                                    </ul>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span>
                                        {{ $item->category->name }}
                                    </span> <br>
                                    <span class="italic">
                                        {{ $item->sub_category }}
                                    </span><br>
                                    <span class="italic">
                                        ({{ $item->category->from }})
                                    </span>
                                </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if ($item->is_beans)
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                        YES
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                        NO
                                    </div>
                                @endif
                            </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-4 text-sm">
                                        @if (auth()->user()->can('access', 'view-menu-addons-action'))
                                            <a
                                                class="flex items-center inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                                href="{{ route('menu.addon.index', $item->id) }}"
                                                >
                                                <span><i class="fa-solid fa-cart-shopping"></i> Add-ons</span>
                                            </a>
                                        @endif
                                        @if(auth()->user()->can('access', 'update-menu-action'))
                                            <button
                                                class="flex btn-update-menu items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateMenuModal"
                                                data-branch_id="{{ $item->branch_id ?? '' }}"
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-gray-700">
                                <td colspan="10" class="px-4 py-3 text-sm text-center">
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

            var addControl = new TomSelect("#select-inventory",{
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: [],
            });

            var updateControl = new TomSelect('#select-update-inventory', {
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: [],
            });

            $(".btn-update-menu").click(function() {
                // Set branch
                var branch_id = $(this).data("branch_id")
                var inventory_id = $(this).data("inventory");

                //  Trigger change for the correct branch to apply
                $("#updateBranch").val(branch_id).trigger('change');
            });

            $("#addBranch").change(function() {
                addControl.clear();
                addControl.clearOptions();

                var selectedItem = $(this).val();
                var inventories = $('option:selected',this).data("inventories");

                inventories.forEach(inventory => {
                    addControl.addOption({
                        id: inventory.id,
                        name: inventory.name
                    });
                });
            });

            $("#updateBranch").change(function(e, data) {
                updateControl.clear();
                updateControl.clearOptions();

                var inventory_id = Alpine.store('menu').updateMenuData.inventory_id

                var selectedItem = $(this).val();
                var inventories = $('option:selected',this).data("inventories");

                inventories.forEach(inventory => {
                    updateControl.addOption({
                        id: inventory.id,
                        name: inventory.name
                    });

                    if (inventory_id == inventory.id) {
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
