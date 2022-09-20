<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('transactions', {
                    data: []
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{  __("Transactions") }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div>
            <a
                href="{{ route('bank.accounts.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>

        <div class="flex space-x-2 jusify-center">
            <button
            type="button"
            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
            data-bs-toggle="modal"
            data-bs-target="#zeroOutAccountModal"
            @click="$store.transactions.data={{ json_encode($account) }}"
            >
            <i class="fa-solid fa-wallet"></i> Reset Balance
        </button>
        </div>

    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Order ID</th>
                    <th class="px-4 py-3">Transaction</th>
                    <th class="px-4 py-3">Previous Balance</th>
                    <th class="px-4 py-3">Running Balance</th>
                    <th class="px-4 py-3 text-center">Credit</th>
                    <th class="px-4 py-3 text-center">Debit</th>
                    <th class="px-4 py-3">Last updated</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($transactions as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->order_id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->action }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->prev_bal }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->running_bal }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-green-500">
                                {{ $item->amount > 0 ? $item->amount : 0}}
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-red-500">
                                {{ $item->amount < 0 ? $item->amount : 0}}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($item->updated_at)->format('M-d-Y g:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="8" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('bank_accounts.modals.zeroout_account')
</x-app-layout>
