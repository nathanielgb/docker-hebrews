<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="addMenuAddonsModal"
    tabindex="-1"
    aria-labelledby="addMenuAddonsModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="addMenuAddonsModalLabel">
                    Add Menu Add-ons
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="add-menu-form" action="{{ route('menu.addon.store', request()->menu) }}" method="post">
                    @csrf
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
                        <p class="text-xs text-yellow-500">note: select in which order type will the add-on apply.</p>
                    </label>

                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Inventory</span>
                        <select id="select-inventory" name="inventory" class="select-inventories mt-1" placeholder="Select inventory..."></select>
                        <p class="text-xs text-yellow-500">note: unit per add-on will be defaulted to 1.</p>
                    </label>

                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Quantity</span>
                        <input class="styled-input" name="qty" type="number" min="1"  placeholder="Enter quantity" required>
                    </label>

                    {{-- <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Inventory</span>
                        <select
                            id="select-inventory"
                            name="inventory"
                            placeholder="Enter Inventory..."
                            autocomplete="off"
                            class="block w-full rounded-sm cursor-pointer focus:outline-none"
                        >
                            <option value="" selected disabled>Select inventory</option>
                        </select>
                    </label> --}}

                    {{-- <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Inventory</span>
                        <select class="styled-input" name="inventory">
                            <option value="" selected disabled>Select inventory</option>
                            @foreach ($inventory_items as $i_item)
                                <option value="{{ $i_item->id }}" >{{ $i_item->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-yellow-500">note: ordering this item will deduct the quantity to the stock of the selected inventory</p>
                    </label> --}}
                </form>
            </div>
            <div
                class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
                >
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"                    data-bs-dismiss="modal"
                    >
                    Close
                </button>
                <button
                    form="add-menu-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    ADD
                </button>
            </div>
        </div>
    </div>
</div>
