<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('discount', {
                    deleteData: [],
                    updateData: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Discounts') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-end">
        <div class="flex space-x-2 my-2">
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#addDiscountModal"
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
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">is Active</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($discounts as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($item->active)
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-green-500 text-white rounded-full">Active</span>
                                @else
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-600 text-white rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->type }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->amount }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (auth()->user()->can('access', 'manage-discounts-action'))
                                    <div class="flex items-center justify-center space-x-2 text-sm">
                                        <button
                                            class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"                                            aria-label="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateDiscountModal"
                                            @click="$store.discount.updateData={{ json_encode([
                                                'id' => $item->id,
                                                'name' => $item->name,
                                                'amount' => $item->amount,
                                                'type' => $item->type,
                                                'active' => $item->active == 1 ? true : false,
                                            ]) }}"
                                            >
                                            <span><i class="fa-solid fa-pen"></i> Update</span>
                                        </button>
                                        <button
                                            @click="$store.discount.deleteData={{ json_encode([
                                                'id' => $item->id,
                                                'name' => $item->name,
                                            ]) }}"
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"                                            aria-label="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteDiscountModal"
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
        @if ($discounts->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $discounts->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('discount.modals.update_discount')
    @include('discount.modals.add_discount')
    @include('discount.modals.delete_discount')


</x-app-layout>
