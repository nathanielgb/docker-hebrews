<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;

class ProductionDashboard extends Component
{
    public $orders;

    public function updateData ()
    {
        $orders = Order::with(['items' => function ($query) {
            $query->with('addons');
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');

        }])->whereHas('items', function ($query) {
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');
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
        return view('livewire.production-dashboard');
    }
}
