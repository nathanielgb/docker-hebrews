<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function index (Request $request)
    {
        $customers = new Customer();
        $customers = $customers->orderBy('name')->paginate(20);

        return view('customers.index',compact('customers'));
    }

    //
    public function view (Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();

        if ($customer) {
            $transactions = Order::where('customer_id', $customer->id)->orderBy('created_at', 'DESC')->paginate(20);

            return view('customers.sections.transactions', compact('transactions'));
        }
        return redirect()->back()->with('error', 'Customer account does not exist.');
    }

    //
    public function store (Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'contact_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Account (name: '. $request->name .') has been successfully added.');
    }

    //
    public function delete (Request $request)
    {
        $account = Customer::where('id', $request->id)->first();

        if ($account) {
            $account->delete();
            return back()->with('success', 'Account has been successfully removed.');
        }

        return redirect()->back()->with('error', 'Customer account does not exist.');
    }
}
