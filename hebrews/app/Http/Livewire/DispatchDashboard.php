<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;

class DispatchDashboard extends Component
{
    public $orders;

    public function updateData ()
    {
        $orders = Order::with(['items' => function ($query) {
            $query->where('dispatcher_cleared', false);
            $query->where('from', '=', 'kitchen');

        }])->whereHas('items', function ($query) {
            $query->where('dispatcher_cleared', false);
            $query->where('from', '=', 'kitchen');
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
        return view('livewire.dispatch-dashboard');
    }
}
