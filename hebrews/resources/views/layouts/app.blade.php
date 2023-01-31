<!DOCTYPE html>
<html x-data="data" lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href=" {{mix ('css/style.css')}}">
        <link rel="shortcut icon" href="{{ asset('hebrews.ico') }}">

        <style>
            [x-cloak] {
                display: none !important;
            }

            .loader {
                border-top-color: #3498db;
                -webkit-animation: spinner 1.5s linear infinite;
                animation: spinner 1.5s linear infinite;
                position: fixed;

                z-index: 9999;
            }

            @-webkit-keyframes spinner {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
            }

            .centered {
                position: fixed;
                top: 50%;
                left: 50%;
                margin-top: -50px;
                margin-left: -50px;
                z-index: 100;
            }

            #overlay {
                position: fixed; /* Sit on top of the page content */
                width: 100%; /* Full width (cover the whole page) */
                height: 100%; /* Full height (cover the whole page) */
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5); /* Black background with opacity */
                z-index: 99; /* Specify a stack order in case you're using a different order for other elements */
                cursor: pointer; /* Add a pointer on hover */
            }
        </style>
        {{ $styles ?? null }}

        <!-- Scripts -->
        <script src="{{ asset('js/init-alpine.js') }}"></script>
        @livewireStyles
        {{ $headerscript ?? null }}

</head>
<body x-cloak>
    <div x-show="loading" id="overlay" class="flex items-center justify-center">
        <div x-show="loading" class="flex items-center justify-center">
            <div class="w-64 h-64 ease-linear border-8 border-t-8 border-gray-200 rounded-full loader"></div>
        </div>
    </div>


    <div
        class="flex h-screen bg-gray-50"
        :class="{ 'overflow-hidden': isSideMenuOpen }"
    >
        <!-- Desktop sidebar -->
        @include('layouts.navigation')
        <!-- Mobile sidebar -->
        <!-- Backdrop -->
        {{-- @include('layouts.navigation-mobile') --}}
        <div class="flex flex-col flex-1 w-full">
            @include('layouts.top-menu')
            <main class="h-full overflow-y-auto">
                <div class="container grid px-6 mx-auto" style="margin-bottom: 50px;">
                    <h2 class="my-6 text-2xl font-semibold text-gray-700">
                        {{ $header ?? null }}
                    </h2>

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
<script src="{{ asset('js/app.js') }}"></script>
{{ $scripts ?? null }}

</html>
