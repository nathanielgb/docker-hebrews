<?php
namespace App\Services;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\DB;


class MenuService
{

    public function createMenu(Menu $menu)
    {
        // if ($order->invoice()->exists()) {
        //     throw new \Exception('Order already has an invoice');
        // }

        return DB::transaction(function() use ($menu) {
            $invoice = $menu->invoice()->create();
            // $this->pushStatus($order, 2);

            return $invoice;
        });
    }


}
