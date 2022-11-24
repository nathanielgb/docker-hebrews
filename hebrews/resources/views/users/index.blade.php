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
        {{ __('Users') }}
    </x-slot>


    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
    </x-slot>

    @include('components.alert-message')


    <div class="flex justify-between my-3">
        <div>
            @if(auth()->user()->can('access', 'manage-branches-action'))
                <a
                    href="{{ route('users.view_branches') }}"
                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span>BRANCHES</span>
                </a>
            @endif
        </div>

        <div>
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#addUserModal"
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
                    <th class="px-4 py-3">User ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">Branch</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-2 py-3">Admin type</th>
                    <th class="px-2 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($users as $user)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $user->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $user->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if ($user->branch)
                                    {{ $user->branch->name }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $user->username }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $user->type }}
                            </td>
                            </td>
                            <td class="px-4 py-3">

                                <div class="flex flex-col items-center space-y-2 text-sm">
                                    @if(auth()->user()->can('access', 'manage-user-action'))
                                        @if (auth()->user()->type == 'MANAGER' && $user->type == 'MANAGER')
                                        @else
                                            <div class="flex items-center justify-center space-x-4 text-sm">
                                                <button
                                                    class="flex btn-update-user items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                                    type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#resetUserModal"
                                                    data-userbranch="{{ $user->branch_id }}"
                                                    @click="$store.data.update={{ json_encode([
                                                        'id' => $user->id,
                                                        'name' => $user->name,
                                                        'type' => $user->type,
                                                    ]) }}"
                                                    >
                                                    <span><i class="fa-solid fa-pen"></i> Update</span>
                                                </button>
                                                <button
                                                    class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"                                            aria-label="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    @click="$store.data.delete={{ json_encode([
                                                        'id' => $user->id,
                                                        'name' => $user->name,
                                                    ]) }}"
                                                    >
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            @endif
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
        @if ($users->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $users->links() }}
            </div>
        @endif
    </div>
    @include('users.modals.delete')
    @include('users.modals.reset')
    @include('users.modals.add')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            new TomSelect('#select-branch', {
                create: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
            var updateControl = new TomSelect('#select-update-user-branch', {
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: [],
            });

            $(".btn-update-user").click(function() {
                updateControl.clear();
                updateControl.clearOptions();

                var userbranch = $(this).data("userbranch");
                var branches = @json($branches);

                branches.forEach(branch => {
                    updateControl.addOption({
                        id: branch.id,
                        name: branch.name
                    });
                    console.log(userbranch)
                    console.log(branch.id)
                    if (userbranch == branch.id) {
                        updateControl.addItem(branch.id);
                    }
                });
            });
        </script>
    </x-slot>
</x-app-layout>
