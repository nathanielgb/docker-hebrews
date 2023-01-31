<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('data', {
                    delete: [],
                    update: [],
                })
            })
        </script>

    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
        <style>
            .sub-cat {
                width: 100%;
                text-align:justify;
                word-break: break-all;
                white-space: pre-line;
                overflow-wrap: break-all;
                -ms-word-break: break-all;
                word-break: break-all;
                -ms-hyphens: auto;
                -moz-hyphens: auto;
                -webkit-hyphens: auto;
                hyphens: auto;
            }
        </style>
    </x-slot>


    <x-slot name="header">
        {{ __('Inventory - Categories') }}
    </x-slot>

    @include('components.alert-message')

    <div class="inline-flex w-full mt-2 mb-4 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-center w-12 bg-yellow-400">
            <i class="text-lg text-white fa-solid fa-circle-exclamation"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-yellow-400">Warning</span>
                <p class="text-sm text-gray-600">Cannot delete categories with linked items.</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end my-3">

        {{-- <div>
            <a
                href="{{ route('menu.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div> --}}

        <button
            type="button"
            class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
            data-bs-toggle="modal"
            data-bs-target="#addCategoryModal"
            >
            <i class="fa-solid fa-circle-plus"></i> ADD
        </button>
    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">Linked Warehouse Inventories</th>
                    <th class="px-4 py-3 text-center">Linked Branch Inventories</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($categories as $category)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $category->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $category->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ count($category->wareHouseInventories) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ count($category->branchInventories) }}
                            </td>
                            <td class="px-4 py-3 text-center">

                                @if(auth()->user()->can('access', 'manage-categories-action'))
                                    @if (count($category->wareHouseInventories) <= 0 && count($category->branchInventories) <= 0)
                                        <div class="flex items-center justify-center space-x-4 text-sm">
                                            <button
                                                type="button"
                                                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                                aria-label="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                @click="$store.data.delete={{ json_encode([
                                                    'id' => $category->id,
                                                    'name' => $category->name,
                                                ]) }}">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="4" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
    @include('menu.inventory.category.modals.add_category')
    @include('menu.inventory.category.modals.delete_category')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">

        </script>
    </x-slot>

</x-app-layout>
