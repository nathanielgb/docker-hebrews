<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="editOrderModal"
    tabindex="-1"
    aria-labelledby="editOrderModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="editOrderModalLabel">
                    Confirm
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="edit-order-form" action="{{ route('order.edit', $order->order_id) }}" method="post">
                    @csrf
                    <div>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Dine-in/Take-out</span>
                            <select id="order_type" class="styled-input" name="order_type">
                                <option value="" selected disabled>Select a choice</option>
                                <option value="dinein" @if ($order->order_type == 'dinein') selected @endif>Dine-in</option>
                                <option value="takeout" @if ($order->order_type == 'takeout') selected @endif>Takeout</option>
                            </select>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700 dark:text-gray-400">Select Table/s</span>
                            @php
                                $tables = isset($order->table) ? $order->table : [];
                            @endphp
                            <select
                                id="select-table"
                                name="tables[]"
                                placeholder="Select tables..."
                                autocomplete="off"
                                class="block w-full rounded-sm cursor-pointer focus:outline-none"
                                multiple
                            >
                                <option value="">none</option>
                                <option value="1" @if (in_array('1', $tables)) selected @endif>Table 1</option>
                                <option value="2" @if (in_array('2', $tables)) selected @endif>Table 2</option>
                                <option value="3" @if (in_array('3', $tables)) selected @endif>Table 3</option>
                                <option value="4" @if (in_array('4', $tables)) selected @endif>Table 4</option>
                                <option value="4" @if (in_array('5', $tables)) selected @endif>Table 5</option>
                                <option value="5" @if (in_array('6', $tables)) selected @endif>Table 6</option>
                                <option value="6" @if (in_array('7', $tables)) selected @endif>Table 7</option>
                                <option value="7" @if (in_array('8', $tables)) selected @endif>Table 8</option>
                                <option value="8" @if (in_array('9', $tables)) selected @endif>Table 9</option>
                                <option value="9" @if (in_array('10', $tables)) selected @endif>Table 10</option>
                            </select>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Delivery method</span>
                            <select class="styled-input" name="delivery_method">
                                <option value="" selected>Select a courier</option>
                                <option value="grab"  @if ($order->delivery_method == 'grab') selected @endif>Grab</option>
                                <option value="lalamove"  @if ($order->delivery_method == 'lalamove') selected @endif>Lalamove</option>
                                <option value="foodpanda"  @if ($order->delivery_method == 'foodpanda') selected @endif>Food Panda</option>
                                <option value="toktok"  @if ($order->delivery_method == 'toktok') selected @endif>Toktok</option>
                            </select>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Fees</span>
                            <input id="fees-input" class="styled-input" name="fees" step=".01" type="number" min="0"  value="{{ $order->fees }}" placeholder="Enter fees">
                        </label>
                        <label class="block text-sm custom-discount">
                            <span class="text-gray-700">Discount (Custom)</span>
                            <input
                                id="custom-discount-input"
                                class="styled-input"
                                name="custom_discount"
                                step=".01"
                                type="number"
                                min="0"
                                value="{{ $order->discount_amount }}"
                                placeholder="Enter custom discount">
                        </label>
                        <div class="block mb-4 form-check" style="display: inline-flex;">
                            <input name="custom_discount_toggle" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="custom-discount-toggle">
                            <label class="inline-block mt-1 text-xs text-center text-gray-800 form-check-label" for="custom-discount-toggle">
                                (WARNING: checking custom discount will overwrite the existing discount type)
                            </label>
                        </div>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Initial Deposit</span>
                            <input class="styled-input" name="deposit" step=".01" type="number" min="0"  value="{{ $order->deposit_bal }}" placeholder="Enter deposit">
                        </label>
                    </div>
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
                    form="edit-order-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
