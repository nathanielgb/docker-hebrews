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
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');

        }])->whereHas('items', function ($query) {
            $query->where('status', '!=', 'served');
            $query->where('production_cleared', false);
            $query->where('from', '=', 'storage');
        })
        ->where('cancelled', false)
        ->where('completed', false)
        ->where('confirmed', true)
        ->orderBy('created_at', 'ASC')
        ->get();

        $this->orders = $orders;
    }

    public function render()
    {
        return view('livewire.production-dashboard');
    }
}