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
        {{ __('Categories') }}
    </x-slot>

    @include('components.alert-message')

    <div class="inline-flex w-full mt-2 mb-4 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-center w-12 bg-yellow-400">
            <i class="text-lg text-white fa-solid fa-circle-exclamation"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-yellow-400">Warning</span>
                <p class="text-sm text-gray-600">Cannot update/delete categories with linked menu items.</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between my-3">

        <div>
            <a
                href="{{ route('menu.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>

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
                    <th class="px-4 py-3">Category ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">Sub-Categories</th>
                    <th class="px-4 py-3 text-center">From</th>
                    <th class="px-4 py-3 text-center">Linked Products</th>
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
                            <td class="w-64 px-4 py-3 text-sm text-center">
                                @if ($category->sub)
                                    <p class="sub-cat">{{ implode(", ",$category->sub) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $category->from }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ count($category->menus) }}
                            </td>
                            <td class="px-4 py-3 text-center">

                                @if(auth()->user()->can('access', 'manage-categories-action') && count($category->menus) <= 0)
                                    <div class="flex items-center justify-center space-x-4 text-sm">
                                        <button
                                            class="flex btn-update-category items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            type="button"
                                            data-sub="{{ json_encode($category->sub) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateModal"
                                            @click="$store.data.update={{ json_encode([
                                                'id' => $category->id,
                                                'name' => $category->name,
                                                'from' => $category->from
                                            ]) }}">
                                            <span><i class="fa-solid fa-pen"></i> Update</span>
                                        </button>
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
                            </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="7" class="px-4 py-3 text-sm text-center">
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
    @include('menu.modals.add_category')
    @include('menu.modals.update_category')
    @include('menu.modals.delete_category')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            new TomSelect('#select-subcat', {
                persist: false,
                createOnBlur: true,
                create: true,
                plugins: ['remove_button'],
            });
            var updateControl = new TomSelect('#select-updatesubcat', {
                persist: false,
                createOnBlur: true,
                create: true,
                valueField: 'sub',
                labelField: 'sub',
                searchField: 'sub',
                options: [],
                plugins: ['remove_button'],
            });

            $(".btn-update-category").click(function() {
                updateControl.clear();
                updateControl.clearOptions();

                var sub = $(this).data("sub");
                sub.forEach(element => {
                    updateControl.addOption({
                        sub: element
                    });
                    updateControl.addItem(element);
                });
            });
        </script>
    </x-slot>

</x-app-layout>
