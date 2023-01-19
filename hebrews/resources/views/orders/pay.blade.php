<x-app-layout>

    {{-- <x-slot name="header">
        {{ __('Pay Order') }}
    </x-slot> --}}

    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2 md:px-28" style="max-width: 850px;">
        <h2 class="my-3 text-2xl font-semibold text-gray-700">Pay Order</h2>
        @include('components.alert-message')

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <form id="pay_form" action="{{ route('order.pay',$order->order_id) }}" method="post" autocomplete="off">
                @csrf
                {{--  <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Credit to Bank Account</span>
                    <select class="styled-input" name="account" required>
                        <option value="{{ $account->id }}">{{ $account->bank }} - {{ $account->account_name }} @if ($account->account_number) ({{ $account->account_number }}) @endif</option>
                    </select>
                </label> --}}
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Cash Input</span>
                    <input id="given-input" name="input_amt" min="0.01" step=".01" class="styled-input" type="number" placeholder="Enter Amount received">
                </label>

                <div class="flex flex-col justify-center px-6 py-5 mb-6 text-base text-gray-500 bg-gray-100 rounded-lg">
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div class="btn inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-blue-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                TOTAL AMOUNT DUE
                            </div>
                            <input id="ord-total" name="total" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div
                                class="btn inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600 hover:shadow-lg focus:bg-blue-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center"
                                type="button"
                                >
                                INITIAL DEPOSIT
                            </div>
                            <input id="initial-dep" name="initial_dep" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div
                                class="btn inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600 hover:shadow-lg focus:bg-blue-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center"
                                type="button"
                                >
                                CASH GIVEN
                            </div>
                            <input id="cash-given" name="initial_dep" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div
                                class="btn inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600 hover:shadow-lg focus:bg-blue-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center"
                                type="button"
                                >
                                TOTAL AMOUNT GIVEN
                            </div>
                            <input id="total-cash-given" name="amt_received" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    {{-- <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div
                                class="btn inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600 hover:shadow-lg focus:bg-blue-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center"
                                type="button"
                                id="button-addon2"
                                >
                                SUBTOTAL
                            </div>
                            <input id="ord-subtotal" name="subtotal" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-green-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div class="btn inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                DISCOUNT
                            </div>
                            <input id="ord-discount" name="discount" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-green-600 focus:outline-none" readonly>
                        </div>
                    </div> --}}

                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div class="btn inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                REMAINING BALANCE
                            </div>
                            <input id="remaining-bal" name="remaining_bal" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="relative flex flex-wrap items-stretch w-full mb-4 input-group">
                            <div class="btn inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600  focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out flex items-center" type="button" id="button-addon2">
                                CHANGE
                            </div>
                            <input id="ord-cashback" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" readonly>
                        </div>
                    </div>
                </div>

                {{-- <div class="flex justify-center w-full">
                    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">

                        <div class="w-full overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                        <th class="px-4 py-3" colspan="2" class="px-4 py-3">BREAKDOWN</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td class="px-3 py-3 text-left text-blue-600">Amount Given</td>
                                        <td id="amount_given_label" class="px-3 py-3">0.00</td>
                                    </tr>
                                    <tr  class="border-b border-black">
                                        <td class="w-1/2 px-3 py-3 text-left text-red-600">Total Amount</td>
                                        <td class="w-1/2 px-3 py-3 ">{{ $order->total_amount }}</td>
                                    </tr>
                                    <tr >
                                        <td class="px-3 py-3 text-left text-green-600">Change</td>
                                        <td  class="px-3 py-3">
                                            <div class="inline-flex items-center px-3 py-1 font-bold text-blue-700 uppercase bg-blue-200 rounded ">
                                                <span id="cashback">0.00</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div> --}}

                <div class="flex justify-center space-x-4">
                    <a
                        href="{{ route('order.show_summary',$order->order_id) }}"
                        class="text-center inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                        >
                        <span>BACK</span>
                    </a>
                    <button
                        type="button"
                        id="pay_btn"
                        class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                        data-bs-toggle="modal"
                        data-bs-target="#completeOrderModal"
                        disabled
                        >
                        PAY
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('orders.modals.pay')

    <x-slot name="scripts">
        <script type="text/javascript">
            calculateOrder()

            function calculateOrder (amount_given=0) {
                var total = {{ $order->total_amount }} || 0;
                var deposit_bal = {{ $order->deposit_bal }} || 0;
                var cash_given = {{ $order->amount_given }} || 0;
                var confirmed_amount = deposit_bal + cash_given + amount_given;
                var remaining_bal = confirmed_amount - total;

                if (amount_given <= 0) {
                    $('#pay_btn').prop('disabled', true);
                    $( '#pay_btn' ).removeClass( "bg-green-800" );
                    $( '#pay_btn' ).addClass( "bg-gray-600" );
                } else {
                    $('#pay_btn').prop('disabled', false);
                    $('#pay_btn').show();
                    $( '#pay_btn' ).removeClass( "bg-gray-600" );
                    $( '#pay_btn' ).addClass( "bg-green-800" );
                }

                if (remaining_bal < 0) {
                    var cashback = 0;
                } else {
                    var cashback = remaining_bal
                    remaining_bal = 0;
                }

                $('#total-cash-given').val(confirmed_amount.toFixed(2));
                $('#initial-dep').val(deposit_bal.toFixed(2));
                $('#cash-given').val(cash_given.toFixed(2));
                $('#remaining-bal').val(remaining_bal.toFixed(2));
                $('#ord-total').val(total.toFixed(2));
                $('#ord-cashback').val(cashback.toFixed(2));
            }

            $('#given-input').on("input", function() {
                var amount_given = parseFloat(this.value) || 0;
                calculateOrder(amount_given)
            });

            $('#confirm_payment').one("click", function() {
                $( "#pay_form" ).submit();
            });
        </script>
    </x-slot>

</x-app-layout>
