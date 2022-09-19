<x-guest-layout>
    <div class="flex justify-center overflow-y-auto md:flex-row">

        <div class="flex items-center justify-center w-full p-6 sm:p-12">
            <div class="w-full">
                <h1 class="mb-4 text-xl font-semibold text-gray-700">
                    Login
                </h1>

                <x-auth-validation-errors :errors="$errors"/>

                <form method="POST" action="{{ route('login') }}">
                @csrf

                    <!-- Input[ype="username"] -->
                    <div class="mt-4">
                        <x-label :value="__('Username')"/>
                        <x-input type="text"
                                 id="username"
                                 name="username"
                                 value="{{ old('username') }}"
                                 class="block w-full"
                                 required
                                 autofocus/>
                    </div>

                    <!-- Input[ype="password"] -->
                    <div class="mt-4">
                        <x-label for="password" :value="__('Password')"/>
                        <x-input type="password"
                                 id="password"
                                 name="password"
                                 class="block w-full"/>
                    </div>

                    <div class="flex mt-6 text-sm">
                        <label class="flex items-center dark:text-gray-400">
                            {{-- <input type="checkbox"
                                   name="remember"
                                   class="text-green-600 form-checkbox focus:border-green-500 focus:outline-none focus:shadow-outline-green">
                            <span class="ml-2">{{ __('Remember me') }}</span> --}}
                        </label>
                    </div>

                    <div class="mt-4">
                        <x-button class="block w-full">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>

                {{-- <hr class="my-8"/>

                @if (Route::has('password.request'))
                    <p class="mt-4">
                        <a class="text-sm font-medium text-primary-600 hover:underline"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </p>
                @endif --}}
            </div>
        </div>
    </div>
</x-guest-layout>
