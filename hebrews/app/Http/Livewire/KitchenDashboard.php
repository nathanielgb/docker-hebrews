<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;

class KitchenDashboard extends Component
{
    public $orders;

    public function updateData ()
    {
        $orders = Order::with(['items' => function ($query) {
            $query->with('addons');
            $query->where('kitchen_cleared', false);
            $query->where('from', '=', 'kitchen');

        }])->whereHas('items', function ($query) {
            $query->where('kitchen_cleared', false);
            $query->where('from', '=', 'kitchen');
        })
        ->where('cancelled', false)
        ->where('confirmed', true);

        if (auth()->user()->branch_id != null) {
            $orders = $orders->where('branch_id', auth()->user()->branch_id);
        }
        $orders = $orders->orderBy('created_at', 'desc')->get();

        $this->orders = $orders;
    }

    public function render()
    {
        return view('livewire.kitchen-dashboard');
    }
}
