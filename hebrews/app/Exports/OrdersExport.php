<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{

    protected $filters;

    function __construct($filters) {
        $this->filters = (object) $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $filters = $this->filters;

        $orders = Order::with('items.addons')->where(function ($query) use ($filters) {
            if (isset($filters->date) && $filters->date !== null) {
                $date_range = explode('-', str_replace(' ', '', $filters->date));
                $start_date = Carbon::parse($date_range[0])->startOfDay();
                $end_date = Carbon::parse($date_range[1])->endOfDay();
                $query->whereBetween('updated_at', [$start_date, $end_date]);
            }
            if (isset($filters->order_id) && $filters->order_id !== null) {
                $_ord_numbers = str_replace(' ', '', $filters->order_id);
                $ord_numbers = explode(',', $_ord_numbers);
                $query->whereIn('order_id', $ord_numbers);
            }
            if (isset($filters->status) && $filters->status !== null) {
                if ($filters->status == 'pending') {
                    $query->where('pending', 1);
                } else if ($filters->status == 'confirmed') {
                    $query->where('confirmed', 1);
                } else if ($filters->status == 'completed') {
                    $query->where('completed', 1);
                } else if ($filters->status == 'cancelled') {
                    $query->where('cancelled', 1);
                }
            }
            if (isset($filters->branch_id) && $filters->branch_id !== null) {
                $query->where('branch_id', $filters->branch_id);
            }
            if (isset($filters->servername) && $filters->servername !== null) {
                $query->where('server_name', 'LIKE', '%' . $filters->servername . '%');
            }
            if (isset($filters->customer_name) && $filters->customer_name !== null) {
                $query->where('customer_name', 'LIKE', '%' . $filters->customer_name . '%');
            }
        });

        $orders = $orders->orderBy('created_at', 'desc')->get();

        return $orders;
    }

    public function map($order): array
    {
        return [
            $order->order_id
        ];
    }

    public function headings(): array
    {
        return [
            'Order ID'
        ];
    }
}