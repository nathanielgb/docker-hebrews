<div
    class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="searchModal"
    tabindex="-1"
    aria-labelledby="searchModalTitle"
    aria-modal="true"
    role="dialog"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding">
        <div class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md">
            <h5 class="text-xl font-medium leading-normal text-gray-800" id="searchModalTitle">
                Search
            </h5>
            <button type="button"
                class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="relative p-4 modal-body">
            <form id="search-menu-form" action="{{ route('logs.inventory.index') }}" method="get" autocomplete="off">
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Module</span>
                    <select class="styled-input" name="module">
                        <option value="" selected disabled>Select a module</option>
                        <option value="order" selected>Order</option>
                        {{-- <option value="branch-inventory" >Branch Inventory</option>
                        <option value="warehouse" >Warehouse</option> --}}
                    </select>
                </label>

                {{-- <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Section</span>
                    <select class="styled-input" name="section">
                        <option value="" selected disabled>Select a section</option>
                        <option value="add-order-item" >Add Item (order)</option>
                        <option value="confirm-order" >Confirm (order)</option>
                        <option value="add-item" >Add Item (inventory)</option>
                        <option value="update-item" >Update Item (inventory)</option>
                        <option value="transfer-item" >Transfer Item (inventory)</option>
                        <option value="delete-item" >Delete Item (inventory)</option>
                    </select>
                </label> --}}
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Order Number</span>
                    <input class="styled-input" name="order_id" type="text" placeholder="Enter order number">
                </label>
                {{-- <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Branch</span>
                    <select class="styled-input" name="branch_id">
                        <option value="" selected disabled>Select branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </label> --}}
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Inventory Name</span>
                    <input class="styled-input" name="inventory_name" type="text" placeholder="Enter name">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Inventory Code</span>
                    <input class="styled-input" name="inventory_code" type="text" placeholder="Enter code">
                </label>
            </form>
        </div>
        <div
            class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
            >
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-dismiss="modal"
                >
                Close
            </button>
            <button
                form="search-menu-form"
                type="submit"
                class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1">
                Search
            </button>
        </div>
        </div>
    </div>
</div>
