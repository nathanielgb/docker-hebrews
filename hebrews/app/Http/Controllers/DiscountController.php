<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\OrderDiscount;
use App\Http\Requests\StoreDiscountRequest;

class DiscountController extends Controller
{
    //
    public function index (Request $request)
    {
        $discounts = OrderDiscount::paginate('20');

        return view('discount.index', compact('discounts'));
    }

    public function store (StoreDiscountRequest $request)
    {
        OrderDiscount::create([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'active' => isset($request->active) ? true : false
        ]);

        return back()->with('success', 'Successfully added ' . $request->name . '.');
    }

    public function update (StoreDiscountRequest $request)
    {
        $discount = OrderDiscount::where('id', $request->discount_id)->first();
        if ($discount) {
            $discount->update([
                'name' => $request->name,
                'type' => $request->type,
                'amount' => $request->amount,
                'active' => isset($request->active) ? true : false
            ]);
            return redirect()->route('discount.index')->with('success', 'Item (name: ' . $discount->name . ') updated successfully.');
        }

        return redirect()->route('discount.index')->with('error', 'Discount does not exist.');

        OrderDiscount::create([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'active' => isset($request->active) ? true : false
        ]);

        return back()->with('success', 'Successfully added ' . $request->name . '.');
    }

    public function delete(Request $request)
    {
        $discount = OrderDiscount::where('id', $request->id)->delete();

        return redirect()->route('discount.index')->with('success', 'Discount was deleted successfully.');
    }
}
