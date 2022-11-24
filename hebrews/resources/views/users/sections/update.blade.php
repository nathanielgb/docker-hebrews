<x-app-layout>

    <x-slot name="header">
        {{ __('Update User') }}
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2">
        @include('components.alert-message')

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <form id="update-user-form" action="{{ route('users.update',['user_id'=>$user->id]) }}" method="post">
                @csrf
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">User ID</span>
                    <input class="block w-full mt-1 text-sm bg-gray-100 border focus:outline-none focus:shadow-outline-gray form-input" disabled readonly value="{{ $user->id }}">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Name</span>
                    <input class="styled-input" name="menu" type="text" placeholder="Cheeseburger" value="{{ $user->name }}">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Username</span>
                    <input class="styled-input" name="dinein_price" type="number" step="0.01" placeholder="2.50" value="{{ $user->username }}">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Admin Type</span>
                    <input class="styled-input" name="takeout_price" type="number" step="0.01" placeholder="3.50" value="{{ $user->type }}">
                </label>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('users.index') }}" class="px-10 py-4 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        <span>BACK</span>
                    </a>
                    <button class="px-10 py-4 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        SAVE
                    </button>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>
