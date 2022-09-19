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

        <div class="flex flex-col text-center">
            <span>Hebrews Cafe</span>
            <span>Cavite</span>

        </div>
        <div style="margin-top : 20px;">
            <table class="w-full table-fixed">
                <thead>
                    <th class="text-left" style="width: 180px;">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                </thead>
                <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td class="text-left break-all" style="width: 180px;">{{ $item->name }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right">{{ $item->total_amount }}</td>
                        </tr>
                    @empty

                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-b-2 border-black border-dashed " style="margin-top: 20px; margin-bottom: 10px; padding-bottom: 10px;">
            <table class="w-full text-right table-fixed">
                <tr>
                    <td>Subtotal</td>
                    <td style="width: 120px;">{{ $order->subtotal }}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td style="width: 120px;">-{{ $order->discount_amount }}</td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td style="width: 120px;">{{ $total_amount }}</td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td style="width: 120px;">{{ $amount_given }}</td>
                </tr>
                <tr>
                    <td>CHANGE</td>
                    <td style="width: 120px;">{{ $cashback }}</td>
                </tr>
            </table>
        </div>
        <div class="flex flex-col text-center " style="margin-bottom: 30px;">
            <span>{{ Carbon\Carbon::now()->format('m/d/Y g:i A') }}</span>
            <span>VAT Reg. TIN: 006-737-173-0000</span>
            <span>BIR Accr. No.: 046-006737173-000611</span>
            <span>Date Issued: 03/01/2013</span>
            <span style="margin-bottom: 5px;">Valid Until: 07/31/2025</span>
            <span>PTUN : FP112019-54B-0239249-00001</span>
            <span>Date Issued: 12/02/2019</span>
            <span style="margin-bottom: 5px;">Valid Until: 12/01/2024</span>
            <span>THIS RECEIPT SHALL BE VALID</span>
            <span>FOR FIVE (5) YEARS</span>
            <span style="margin-bottom: 20px;">FROM THE DATE OF THE PERMIT TO USE.</span>
            <span style="font-size: 14px;">This serves as a Sales Invoice</span>
            <span style="font-size: 14px;">Thank you... Come Again...</span>

        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>


</html>
