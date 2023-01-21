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
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <style>
            div {
                font-family: 'Roboto Mono', monospace;
                font-size: 12px;
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
    <div id="print-btn" class="flex justify-end w-full p-2">
        <button
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                onclick="printRes()">
                <span><i class="fa-solid fa-print"></i> PRINT</span>
            </button>
    </div>

    <div id="receipt" class="mx-auto border border-black container-sm side-padding" style="width: 450px;">
        <!-- ... -->

        <div class="flex flex-col text-center" style="margin-bottom : 30px;">
            <span>Hebrews Kape</span>
        </div>
        <div class="flex flex-col text-left">
            <span>Date: {{ $order->created_at->format('m/d/Y g:i A') }}</span>
            <span>Order: {{ $order->order_id }}</span>
            @if ($order->customer_name)
                <span>Customer: {{ $order->customer_name }}</span>
            @endif
            @if ($order->order_type == 'dinein')
                <span class="flex flex-row">
                    Table/s:&nbsp;                 
                    @if ($order->table)
                        <p>
                            @foreach ($order->table as $table)
                                {{ $table }}@if(!$loop->last),@endif
                            @endforeach
                        </p>
                    @endif
                </span>
            @else
                <span>Delivery: {{ $order->delivery_method }}</span>
            @endif
        </div>
        <div style="margin-top : 20px; margin-bottom : 20px;">
            <table class="w-full table-fixed">
                <thead>
                    <th class="text-left" style="max-width: 120px;">Item</th>
                    <th class="text-center" style="max-width: 120px;">Type</th>
                    <th class="text-center">Tot.Stock</th>
                </thead>
                <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td class="text-left break-all" style="width: 180px;">
                                {{ $item->name }}
                                @if (isset($item->data['grind_type']) && !empty($item->data['grind_type']))
                                    ({{ $item->data['grind_type'] }})
                                @endif
                            </td>
                            <td class="text-center">
                                @if (isset($item->data['is_dinein']) && $item->data['is_dinein'])
                                    dine-in
                                @else
                                    take-out
                                @endif
                            </td>
                            <td class="text-center">{{ $item->qty*$item->units }}({{ $item->unit_label }})</td>
                        </tr>
                    @empty

                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col text-center " style="margin-bottom: 30px;">
            <!-- <span>{{ Carbon\Carbon::now()->format('m/d/Y g:i A') }}</span> -->
        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>


</html>
