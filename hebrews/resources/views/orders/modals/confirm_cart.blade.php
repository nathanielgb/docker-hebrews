<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="confirmCartModal"
    tabindex="-1"
    aria-labelledby="confirmCartModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="confirmCartModalLabel">
                    Confirm
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="confirm-cart-form" action="{{ route('order.generate') }}" method="post">
                    @csrf
                    <div>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Branch</span>
                            <select id="addBranch" class="styled-input" name="branch">
                                <option value="" disabled>Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if(auth()->user()->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Dine-in/Take-out</span>
                            <select class="styled-input" name="order_type">
                                <option value="" selected disabled>Select a choice</option>
                                <option value="dinein">Dine-in</option>
                                <option value="takeout">Takeout</option>
                            </select>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700 dark:text-gray-400">Select Table/s</span>
                            <select
                                x-model="transSelectedTables"
                                id="select-table"
                                name="tables[]"
                                placeholder="Select tables..."
                                autocomplete="off"
                                class="block w-full rounded-sm cursor-pointer focus:outline-none"
                                multiple
                            >
                                <option value="">none</option>
                                <option value="1">Table 1</option>
                                <option value="2">Table 2</option>
                                <option value="3">Table 3</option>
                                <option value="4">Table 4</option>
                                <option value="5">Table 5</option>
                                <option value="6">Table 6</option>
                                <option value="7">Table 7</option>
                                <option value="8">Table 8</option>
                                <option value="9">Table 9</option>
                                <option value="10">Table 10</option>
                            </select>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Delivery method</span>
                            <select class="styled-input" name="delivery_method">
                                <option value="" selected>Select a courier</option>
                                <option value="grab">Grab</option>
                                <option value="lalamove">Lalamove</option>
                                <option value="foodpanda">Food Panda</option>
                                <option value="toktok">Toktok</option>
                            </select>
                        </label>

                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Customer</span>
                            <select class="styled-input" name="customer" id="customer-account">
                                <option value="" selected disabled>Select an account</option>
                                <option value="" >none</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" >{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <input id="customer-name" class="styled-input" name="customer_name"type="text" placeholder="Enter name">
                            <div class="form-check">
                                <input name="noAccount" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="noAccountBox">
                                <label class="inline-block text-gray-800 form-check-label" for="flexCheckChecked">
                                    no account
                                </label>
                            </div>
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Fees</span>
                            <input id="fees-input" class="styled-input" name="fees" step=".01" type="number" min="0"  value="0" placeholder="Enter fees">
                        </label>
                        <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Discount</span>
                            <select id="order-discounts" class="styled-input" name="discount">
                                <option value="" selected disabled>Select a discount</option>
                                <option value="" >none</option>
                                <option value="custom" data-discount="custom">Custom</option>
                                @foreach ($discounts as $discount)
                                    <option value="{{ $discount->id }}" data-discount="{{ json_encode($discount) }}">{{ $discount->name }} ({{ $discount->amount }}@if($discount->type == 'percentage')%@endif)</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block mb-4 text-sm custom-discount">
                            <span class="text-gray-700">Discount (Custom)</span>
                            <input id="custom-discount-input" class="styled-input" name="custom_discount" step=".01" type="number" min="0" value="0"  placeholder="Enter custom discount">
                        </label>
                        {{-- <label class="block mb-4 text-sm">
                            <span class="text-gray-700">Initial Deposit</span>
                            <input class="styled-input" name="deposit" step=".01" type="number" min="0"  value="0" placeholder="Enter deposit">
                        </label> --}}
                    </div>
                    <hr class="text-gray-300">
                    <div class="my-5">
                        <div class="flex flex-col justify-center px-6 py-5 text-base text-gray-500 bg-gray-100 rounded-lg">
                            <div class="mb-3">
                                <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                                    <div class="btn inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                        SUBTOTAL
                                    </div>
                                    <input id="ord-subtotal" name="subtotal" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-green-600 focus:outline-none" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                                    <div class="btn inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                        FEES
                                    </div>
                                    <input id="ord-fees" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-green-600 focus:outline-none" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                                    <div class="btn inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                        DISCOUNT
                                    </div>
                                    <input id="ord-discount" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-green-600 focus:outline-none" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                                    <div class="btn inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                        TOTAL
                                    </div>
                                    <input id="ord-total" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                                </div>
                            </div>
                        </div>
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
                    form="confirm-cart-form"
                    type="submit"
                    class="cart-submit inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
