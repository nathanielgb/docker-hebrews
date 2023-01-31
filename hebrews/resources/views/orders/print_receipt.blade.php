<!DOCTYPE html>
<html x-data="data" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <!-- Styles -->
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}" media="screen, print">
    <link rel="stylesheet" href="{{ asset('css/pos-full.css') }}" media="screen">
    <style>
        * {
            font-family: 'Nunito Sans', sans-serif;
        }
        #print-btn {
            text-align: center;
        }
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }


        @media print {
        #print-btn{
            display: none;
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
    <div id="print-btn">
        <button
            class="button"
            onclick="printRes()">
            <span><i class="fa-solid fa-print"></i> PRINT</span>
        </button>
    </div>


  <div id="invoice-POS">

    <center id="top">
      <div class="logo"></div>
      <div class="info">
        <h2>Hebrews Kape</h2>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->

    <div id="mid">
      <div class="info">
        <p class="order">
            <span>Date: {{ $order->created_at->format('m/d/Y g:i A') }}</span> <br>
            <span>Order: {{ $order->order_id }}</span> <br>
            <span>Type: {{ $order->order_type }}</span> <br>
            @if ($order->customer_name)
                <span>Customer: {{ $order->customer_name }}</span> <br>
            @endif
            @if ($order->order_type == 'dinein')
                    @if ($order->table != null)
                        <span class="flex flex-row">
                            Table/s:&nbsp;
                            @foreach ($order->table as $table)
                                {{ $table }}@if(!$loop->last),@endif
                            @endforeach
                        </span> <br>
                    @endif
            @else
                <span>Delivery: {{ $order->delivery_method }}</span> <br>
            @endif
          </div>
        </p>

    </div><!--End Invoice Mid-->

    <div id="bot">

        <div id="table">
            <table>
                <tr class="tabletitle">
                    <td class="item"><h2>Item</h2></td>
                    <td class="Hours"><h2>Qty</h2></td>
                    <td class="Rate"><h2>Price</h2></td>
                </tr>

                @forelse ($order->items as $item)
                    <tr class="service">
                        <td class="tableitem">
                            <p class="itemtext">
                                {{ $item->name }}
                                @if ($item->unit_label)
                                    ({{ $item->unit_label }})
                                @endif
                                @if (isset($item->data['grind_type']) && !empty($item->data['grind_type']))
                                    ({{ $item->data['grind_type'] }})
                                @endif
                                @php
                                $addons = $item->addons;
                                @endphp
                                @if (count($addons) > 0)
                                    @foreach($addons as $addon)
                                    <br> * {{ $addon->inventory_name }} x{{ $addon->qty }}
                                    @endforeach
                                @endif
                            </p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">
                                {{ $item->qty*$item->units }}
                            </p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">
                                {{ $item->total_amount }}
                            </p>
                        </td>
                    </tr>
                @empty

                @endforelse

                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Subtotal</h2></td>
                    <td class="payment"><h2>{{ $order->subtotal }}</h2></td>
                </tr>

                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Fees</h2></td>
                    <td class="payment"><h2>{{ $order->fees }}</h2></td>
                </tr>
                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Discount</h2></td>
                    <td class="payment"><h2>-{{ $order->discount_amount }}</h2></td>
                </tr>
                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Total</h2></td>
                    <td class="payment"><h2>{{ $total_amount }}</h2></td>
                </tr>
                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Cash</h2></td>
                    <td class="payment"><h2>{{ $amount_given }}</h2></td>
                </tr>
                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>CHANGE</h2></td>
                    <td class="payment"><h2>{{ $cashback }}</h2></td>
                </tr>
            </table>
        </div><!--End Table-->

        {{-- <div id="legalcopy">
            <p class="legal"><strong>Thank you... Come Again...</strong>
            </p>
        </div> --}}

    </div><!--End InvoiceBot-->
  </div><!--End Invoice-->

</body>
<script src="{{ asset('js/app.js') }}"></script>


</html>
