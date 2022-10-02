{{-- <div
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
></div> --}}
<aside
        class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white md:hidden"
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0 transform -translate-x-20"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform -translate-x-20"
        @click.outside="closeSideMenu"
        @keydown.escape="closeSideMenu"
>
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800" href="{{ route('dashboard') }}">
            77Diner
        </a>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <i class="fa-solid fa-house"></i>
                    </x-slot>
                    {{ __('Dashboard') }}
                </x-nav-link>
            </li>

            @if(auth()->user()->can('access', 'view-users-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-users"></i>
                        </x-slot>
                        {{ __('Users') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-customers-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-users"></i>
                        </x-slot>
                        {{ __('Customers') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-bank-accounts-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('bank.accounts.index') }}" :active="request()->routeIs('bank.accounts.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-wallet"></i>
                        </x-slot>
                        {{ __('Bank Accounts') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-menus-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('menu.index') }}" :active="request()->routeIs('menu.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-book-open"></i>
                        </x-slot>
                        {{ __('Menu') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-menu-addons-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('menu.addon.index') }}" :active="request()->routeIs('menu.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-link"></i>
                        </x-slot>
                        {{ __('Add-ons') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-discounts-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('discount.index') }}" :active="request()->routeIs('discount.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-percent"></i>
                        </x-slot>
                        {{ __('Discount') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'take-orders-action'))
                {{-- <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('order.show_take_order') }}" :active="request()->routeIs('order.show_take_order')">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ionicon" viewBox="0 0 512 512"><title>Clipboard</title><path d="M336 64h32a48 48 0 0148 48v320a48 48 0 01-48 48H144a48 48 0 01-48-48V112a48 48 0 0148-48h32" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="176" y="32" width="160" height="64" rx="26.13" ry="26.13" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/></svg>
                        </x-slot>
                        {{ __('Take New Orders') }}
                    </x-nav-link>
                </li> --}}
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('order.show_add_cart') }}" :active="request()->routeIs('order.show_add_cart')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-clipboard"></i>
                        </x-slot>
                        {{ __('Take Orders') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-order-list-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('order.list') }}" :active="request()->routeIs('order.list')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-receipt"></i>
                        </x-slot>
                        {{ __('Orders') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'generate-order-report-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('expense.report.show') }}" :active="request()->routeIs('expense.report.show')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-money-bill"></i>
                        </x-slot>
                        {{ __('Expense Report') }}
                    </x-nav-link>
                </li>

                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('orders.report.show') }}" :active="request()->routeIs('orders.report.show')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-file-invoice"></i>
                        </x-slot>
                        {{ __('Order Report') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-cook-dashboard-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('kitchen.orders.list') }}" :active="request()->routeIs('kitchen.orders.list')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-utensils"></i>
                        </x-slot>
                        {{ __('Kitchen Orders') }}
                    </x-nav-link>
                </li>
            @endif

            {{-- @if(auth()->user()->can('access', 'view-bar-dashboard-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('bar.orders.list') }}" :active="request()->routeIs('bar.orders.list')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-martini-glass"></i>
                        </x-slot>
                        {{ __('Bar Orders') }}
                    </x-nav-link>
                </li>
            @endif --}}

            @if(auth()->user()->can('access', 'view-dispatch-dashboard-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('dispatch.list') }}" :active="request()->routeIs('disptach.list')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-bell-concierge"></i>
                        </x-slot>
                        {{ __('Dispatch Orders') }}
                    </x-nav-link>
                </li>
            @endif
        </ul>
    </div>
</aside>
