<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    //
    public function index (Request $request)
    {
        $bank_accounts = new BankAccount();
        $bank_accounts = $bank_accounts->orderBy('bank')->paginate(20);

        return view('bank_accounts.index',compact('bank_accounts'));
    }

    //
    public function store (Request $request)
    {
        $request->validate([
            'bank' => 'required|min:1| max:100',
            'account_name' => 'required|min:1|max:100',
            'account_number' => 'nullable|min:1|max:100',
        ]);

        BankAccount::create([
            'bank' => $request->bank,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return back()->with('success', 'Account (name: '. $request->account_name .') has been successfully added.');
    }

    //
    public function delete (Request $request)
    {
        $account = BankAccount::where('id', $request->id)->first();

        if ($account) {
            $account->delete();

            // delete all transactions
            BankTransaction::where('account_id', $request->id)->delete();

            return back()->with('success', 'Account has been successfully removed.');
        }

        return redirect()->back()->with('error', 'Bank account does not exist.');
    }

    public function reset (Request $request)
    {
        $account = BankAccount::where('id', $request->id)->first();
        $running_balance = $account->bal;
        if ($account) {
            $account->update([
                'bal' => 0
            ]);

            // Save transaction record
            BankTransaction::create([
                    'order_id' => null,
                    'account_id' => $account->id,
                    'action' => 'Reset Balance',
                    'amount' => -$running_balance,
                    'running_bal' => 0,
                    'prev_bal' => $running_balance
                ]);

            return back()->with('success', 'Account has been successfully removed.');
        }

        return redirect()->route('bank.accounts.index')->with('error', 'Bank account does not exist.');
    }

    public function showTransactions (Request $request, $id)
    {
        $account = BankAccount::where('id', $id)->first();

        if (!$account) {
            return redirect()->route('bank.accounts.index')->with('error', 'Bank account does not exist.');
        }

        $transactions = BankTransaction::where('account_id', $account->id)->orderBy('updated_at', 'DESC')->paginate(20);

        return view('bank_accounts.sections.transactions', compact('account','transactions'));
    }
}
