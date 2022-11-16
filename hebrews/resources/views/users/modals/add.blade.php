<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="addUserModal"
    tabindex="-1"
    aria-labelledby="addUserModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="addUserModalLabel">
                    Add User
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="add-user-form" method="post" action="{{ route('users.add') }}">
                    @csrf
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Admin Type</span>
                        <select class="styled-input" name="type">
                            <option value="" selected disabled>Select Admin Type</option>
                            @foreach ($admin_types as $type)
                                <option value="{{ $type->name }}" >{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Name</span>
                        <input class="styled-input" name="name" type="text" placeholder="Enter name" required>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Branch</span>
                        <select
                            id="select-branch"
                            name="branch_id"
                            placeholder="Enter Branch..."
                            autocomplete="off"
                            class="block w-full rounded-sm cursor-pointer focus:outline-none"
                        >
                            <option value=""></option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-yellow-500">note:
                            leave empty to access all branches.
                        </p>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Username</span>
                        <input class="styled-input" name="username" type="text" placeholder="enter username" required>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Password</span>
                        <input class="styled-input" name="password" type="password"  placeholder="Enter password" required autocomplete="new-password">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Confirm Password</span>
                        <input class="styled-input" name="password_confirmation" type="password"  placeholder="Confirm password" required>
                    </label>
                </form>
            </div>
            <div
                class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
                >
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"                    data-bs-dismiss="modal"
                    >
                    Close
                </button>
                <button
                    form="add-user-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    ADD
                </button>
            </div>
        </div>
    </div>
</div>
