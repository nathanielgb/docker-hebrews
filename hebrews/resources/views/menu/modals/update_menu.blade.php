<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="updateMenuModal"
    tabindex="-1"
    aria-labelledby="updateMenuModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="updateMenuModalLabel">
                    Update Menu
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="update-menu-form" action="{{ route('menu.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="menu_id" :value="$store.menu.updateMenuData?.id">
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Branch</span>
                        <select id="updateBranch" class="styled-input" name="branch" disabled>
                            <option value="" selected disabled>Select a branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" data-inventories="{{ json_encode($branch->inventories) }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Name</span>
                        <input class="styled-input" name="menu" type="text" placeholder="Cheeseburger" :value="$store.menu.updateMenuData?.name">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Category</span>
                        <select id="updateCategory" class="styled-input" name="category">
                            <option value="" selected disabled>Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" :selected="$store.menu.updateMenuData?.category_id === '{{ $category->id }}'" data-sub="{{ json_encode($category->sub) }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Sub-Category</span>
                        <select x-model="subCat" id="updateSubCategory" class="styled-input" name="sub_category">
                            <option value="" disabled>Select a sub-category</option>
                            <template x-for="subCat in $store.menu.subCategories">
                                <option x-text="subCat" :value="subCat" :selected="$store.menu.updateMenuData?.sub_category === subCat"></option>
                            </template>
                        </select>
                    </label>

                    {{-- <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Inventory</span>
                        <select class="styled-input" name="inventory">
                            <option value="" selected disabled>Select inventory</option>
                            @foreach ($inventory_items as $i_item)
                                <option value="{{ $i_item->id }}" :selected="$store.menu.updateMenuData?.inventory_id === '{{ $i_item->id }}'">{{ $i_item->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-yellow-500">note: ordering this item will deduct the quantity to the stock of the selected inventory</p>
                    </label> --}}

                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Inventory</span>
                        <select
                            id="select-update-inventory"
                            name="inventory"
                            placeholder="Enter Inventory..."
                            autocomplete="off"
                            class="block w-full rounded-sm cursor-pointer focus:outline-none mt-1"
                        >
                            <option value="" selected disabled>Select inventory</option>
                            @foreach ($inventory_items as $i_item)
                                <option value="{{ $i_item['id'] }}">{{ $i_item['name'] }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-yellow-500">note: ordering this item will deduct the quantity to the stock of the selected inventory</p>
                    </label>

                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">No. of Unit</span>
                        <input class="styled-input"
                            name="unit"
                            type="number"
                            step=".001"
                            min="0"
                            placeholder="1"
                            :value="$store.menu.updateMenuData?.units">
                        <p class="text-xs text-yellow-500">note: number of units to be deducted to the current stock of inventory for every order</p>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Regular Price</span>
                        <input class="styled-input" name="reg_price" type="number" min="0" step="0.01" placeholder="2.50" :value="$store.menu.updateMenuData?.reg_price">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Retail Price</span>
                        <input class="styled-input" name="retail_price" type="number" min="0" step="0.01" placeholder="2.50" :value="$store.menu.updateMenuData?.retail_price">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Wholesale Price</span>
                        <input class="styled-input" name="wholesale_price" type="number" min="0" step="0.01" placeholder="3.50" :value="$store.menu.updateMenuData?.wholesale_price">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Distributor Price</span>
                        <input class="styled-input" name="distributor_price" type="number" min="0" step="0.01" placeholder="3.50" :value="$store.menu.updateMenuData?.distributor_price">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Rebranding Price</span>
                        <input class="styled-input" name="rebranding_price" type="number" min="0" step="0.01" placeholder="3.50" :value="$store.menu.updateMenuData?.rebranding_price">
                    </label>
                    <div class="form-check">
                        <input name="is_beans" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="flexCheckChecked" :checked="$store.menu.updateMenuData?.is_beans ? true : false">
                        <label class="inline-block text-gray-800 form-check-label" for="flexCheckChecked">
                            Is Beans
                        </label>
                    </div>
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
                    form="update-menu-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
