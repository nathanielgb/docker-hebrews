<aside
    x-show="isSideMenuOpen"
    x-transition:enter="transition ease-in-out duration-150"
    x-transition:enter-start="opacity-0 transform -translate-x-20"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 transform -translate-x-20"
    class="z-20 flex-shrink-0 w-64 overflow-y-auto bg-white">
    <div class="py-4 text-gray-500">
        <a class="ml-6 text-lg font-bold text-gray-800" href="{{ route('dashboard') }}">
            Hebrews Kape
        </a>

        {{-- <div class="container-fluid">
            <a class="flex items-center mt-2 ml-6 mr-1 text-gray-900 hover:text-gray-900 focus:text-gray-900 lg:mt-0" href="{{ route('dashboard') }}">
                <img class="mr-2" src="{{ asset('hebrews.ico') }}" style="height: 50px" alt="" loading="lazy" />
                <span class="text-lg font-medium">Hebrews</span>
            </a>
        </div> --}}

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

            @if(auth()->user()->can('access', 'view-menus-action') || auth()->user()->can('access', 'view-menu-addons-action'))
                <li class="relative px-6 py-3">
                    <x-nav-dropdown-link
                        data-bs-target="#collapseMenu"
                        aria-controls="collapseMenu"
                        :active="request()->routeIs('menu.index') || request()->routeIs('menu.addon.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-book-open"></i>
                        </x-slot>
                        {{ __('Menu') }}
                    </x-nav-dropdown-link>
                    <ul
                        class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner collapse bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                        id="collapseMenu"
                    >
                        @if(auth()->user()->can('access', 'view-menus-action'))
                            <li
                                @if (request()->routeIs('menu.index'))
                                    class="px-2 py-1 text-green-700"
                                @else
                                    class="px-2 py-1"
                                @endif >
                                <a class="w-full" href="{{ route('menu.index') }}">Menu</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(auth()->user()->can('access', 'view-inventory-action') || auth()->user()->can('access', 'view-branch-inventory-action'))
                <li class="relative px-6 py-3">
                    <x-nav-dropdown-link
                        data-bs-target="#collapseInventory"
                        aria-controls="collapseInventory"
                        :active="request()->routeIs('menu.view_inventory') || request()->routeIs('branch.inventory.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-warehouse"></i>
                        </x-slot>
                        {{ __('Inventory') }}
                    </x-nav-dropdown-link>
                    <ul
                        class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner collapse bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                        id="collapseInventory"
                    >
                        @if(auth()->user()->can('access', 'view-inventory-action'))
                            <li
                                @if (request()->routeIs('menu.view_inventory'))
                                    class="px-2 py-1 text-green-700"
                                @else
                                    class="px-2 py-1"
                                @endif >
                                <a class="w-full" href="{{ route('menu.view_inventory') }}">Warehouse</a>
                            </li>
                        @endif
                        @if (auth()->user()->can('access', 'view-branch-inventory-action'))
                            <li
                                @if (request()->routeIs('branch.inventory.index'))
                                    class="px-2 py-1 text-green-700"
                                @else
                                    class="px-2 py-1"
                                @endif >
                                <a class="w-full" href="{{ route('branch.inventory.index') }}">
                                    Branches
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('access', 'import-inventory-action'))
                            <li
                                @if (request()->routeIs('branch.inventory.import.show'))
                                    class="px-2 py-1 text-green-700"
                                @else
                                    class="px-2 py-1"
                                @endif >
                                <a class="w-full" href="{{ route('branch.inventory.import.show') }}">
                                    Import
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('access', 'view-inventory-category-action'))
                            <li
                                @if (request()->routeIs('branch.inventory.import.show'))
                                    class="px-2 py-1 text-green-700"
                                @else
                                    class="px-2 py-1"
                                @endif >
                                <a class="w-full" href="{{ route('menu.inventories.categories') }}">
                                    Categories
                                </a>
                            </li>
                        @endif
                    </ul>
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
                    <x-nav-dropdown-link
                        data-bs-target="#collapsReport"
                        aria-controls="collapsReport"
                        :active="request()->routeIs('expense.report.show') || request()->routeIs('orders.report.show')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-file-invoice"></i>
                        </x-slot>
                        {{ __('Report') }}
                    </x-nav-dropdown-link>
                    <ul
                        class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner collapse bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                        id="collapsReport"
                    >
                        <li
                            @if (request()->routeIs('expense.report.show'))
                                class="px-2 py-1 text-green-700"
                            @else
                                class="px-2 py-1"
                            @endif >
                            <a class="w-full" href="{{ route('expense.report.show') }}">Expense Report</a>
                        </li>
                        <li
                            @if (request()->routeIs('orders.report.show'))
                                class="px-2 py-1 text-green-700"
                            @else
                                class="px-2 py-1"
                            @endif >
                            <a class="w-full" href="{{ route('orders.report.show') }}">
                                Order Report
                            </a>
                        </li>
                    </ul>
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

            @if(auth()->user()->can('access', 'view-production-dashboard-action'))
                <li class="relative px-6 py-3">
                    <x-nav-link href="{{ route('production.list') }}" :active="request()->routeIs('production.list')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-box"></i>
                        </x-slot>
                        {{ __('Production Orders') }}
                    </x-nav-link>
                </li>
            @endif

            @if(auth()->user()->can('access', 'view-logs'))
                <li class="relative px-6 py-3">
                    <x-nav-dropdown-link
                        data-bs-target="#collapseLogs"
                        aria-controls="collapseLogs"
                        :active="request()->routeIs('expense.report.show') || request()->routeIs('logs.inventory.index')">
                        <x-slot name="icon">
                            <i class="fa-solid fa-gears"></i>
                        </x-slot>
                        {{ __('Logs') }}
                    </x-nav-dropdown-link>
                    <ul
                        class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner collapse bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                        id="collapseLogs"
                    >
                        {{-- <li
                            @if (request()->routeIs('expense.report.show'))
                                class="px-2 py-1 text-green-700"
                            @else
                                class="px-2 py-1"
                            @endif >
                            <a class="w-full" href="{{ route('expense.report.show') }}">Admin Logs</a>
                        </li> --}}
                        <li
                            @if (request()->routeIs('logs.inventory.index'))
                                class="px-2 py-1 text-green-700"
                            @else
                                class="px-2 py-1"
                            @endif >
                            <a class="w-full" href="{{ route('logs.inventory.index') }}">
                                Inventory Logs
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</aside>
