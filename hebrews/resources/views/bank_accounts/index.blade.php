<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('account', {
                    data: []
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Bank Accounts') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-end my-3">
        <div class="flex space-x-2 jusify-center">
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#addAccountModal"
                >
                <i class="fa-solid fa-circle-plus"></i> ADD
            </button>
        </div>

    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Bank</th>
                    <th class="px-4 py-3">Account Name</th>
                    <th class="px-4 py-3">Account Number</th>
                    <th class="px-4 py-3">Running Balance</th>
                    <th class="px-4 py-3">Last updated</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($bank_accounts as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->bank }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->account_name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->account_number }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->bal }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($item->updated_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (auth()->user()->can('access', 'manage-bank-account-action'))
                                    <div class="flex items-center justify-center space-x-4 text-sm">
                                        <a
                                            href="{{ route('bank.account.transactions', $item->id) }}"
                                            class="flex items-center inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-500 hover:shadow-lg focus:bg-green-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-500 active:shadow-lg transition duration-150 ease-in-out"
                                            >
                                            <span><i class="fa-solid fa-eye"></i> View</span>
                                        </a>

                                        <button
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteAccountModal"
                                            @click="$store.account.data={{ json_encode([
                                                'id' => $item->id,
                                                'name' => $item->account_name,
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
                            <td colspan="8" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($bank_accounts->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $bank_accounts->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('bank_accounts.modals.add_account')
    @include('bank_accounts.modals.delete_account')
</x-app-layout>
