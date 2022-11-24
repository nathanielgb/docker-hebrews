<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('data', {
                    update: [],
                    delete: [],
                })
            })

        </script>
    </x-slot>


    <x-slot name="header">
        {{ __('Branches') }}
    </x-slot>

    @include('components.alert-message')


    <div class="flex justify-between my-3">
        <div>
            <a
                href="{{ route('users.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span>BACK</span>
            </a>
        </div>

        <div>
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#addBranchModal"
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
                    <th class="px-4 py-3">Branch ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-2 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($branches as $branch)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $branch->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $branch->name }}
                                @if ($branch->id == 1)
                                    <i class="text-green-700 fa-solid fa-house"></i>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $branch->location }}
                            </td>
                            </td>
                            <td class="px-4 py-3">

                                <div class="flex flex-col items-center space-y-2 text-sm">

                                    @if(auth()->user()->can('access', 'manage-user-action'))
                                        <div class="flex items-center justify-center space-x-4 text-sm">
                                            <button
                                                class="flex btn-update-category items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateBranchModal"
                                                @click="$store.data.update={{ json_encode([
                                                    'id' => $branch->id,
                                                    'name' => $branch->name,
                                                    'location' => $branch->location
                                                ]) }}"
                                                >
                                                <span><i class="fa-solid fa-pen"></i> Update</span>
                                            </button>
                                            @if ($branch->id != 1)
                                                <button
                                                    type="button"
                                                    class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                                    aria-label="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    @click="$store.data.delete={{ json_encode([
                                                        'id' => $branch->id,
                                                        'name' => $branch->name,
                                                    ]) }}"
                                                    >
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
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
        @if ($branches->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
    @include('users.modals.branches.delete')
    @include('users.modals.branches.update')
    @include('users.modals.branches.add')

</x-app-layout>
