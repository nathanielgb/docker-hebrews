<!DOCTYPE html>
<html x-data="data" lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href=" {{mix ('css/style.css')}}">
        <style>
            div {
                font-family: 'Roboto Mono', monospace;
            }

            .side-padding {
                padding: 30px;
            }

            @media print {
			#receipt{
				width: 100% !important;
				margin: 0 !important;
			}

			#print-btn{
				display: none;
			}

			*{
				color: black !important;
			}
		}
        </style>

        <!-- Scripts -->
        <script src="{{ asset('js/init-alpine.js') }}"></script>
        <script>
            function printRes() {
                window.print();
            }
        </script>
</head>
<body>

    <div id="receipt" class="mx-auto container-sm side-padding" style="width: 1080px;">
        <div id="print-btn" class="flex justify-end w-full">
            <button
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                onclick="printRes()">
                <span><i class="fa-solid fa-print"></i> PRINT</span>
            </button>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center border">
                            <thead class="border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                        Qty
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                        Add-on
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                        Total Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->items as $item)
                                    <tr class="border-b">
                                        <td class="px-6 py-4 text-sm font-light text-gray-900 border-r whitespace-nowrap">
                                            {{ $item->name }}
                                            @if (isset($item->data['grind_type']) && !empty($item->data['grind_type']))
                                                ({{ $item->data['grind_type'] }})
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-light text-gray-900 border-r whitespace-nowrap">
                                            {{ $item->qty }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-light text-gray-900 border-r whitespace-nowrap">
                                            <!-- todo -->
                                            -
                                        </td>
                                        <td class="px-6 py-4 text-sm font-light text-gray-900 whitespace-nowrap">
                                            {{ $item->total_amount }}
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>
                            @if (!empty($order->items))
                                <tr class="border-t border-gray-300">
                                    <td class="px-4 py-2 text-sm font-semibold text-right" colspan="3">
                                        Subtotal
                                    </td>
                                    <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                        {{ $order->subtotal }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-sm font-semibold text-right" colspan="3">
                                        Discount Amount
                                    </td>
                                    <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                        -{{ $order->discount_amount }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-sm font-semibold text-right" colspan="3">
                                        Other Fees
                                    </td>
                                    <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                        {{ $order->fees }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-sm font-semibold text-right" colspan="3">
                                        Total Amount
                                    </td>
                                    <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                        {{ $order->total_amount }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>


</html>
