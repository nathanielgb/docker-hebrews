<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccessAction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 'SUPERADMIN' && Auth::user()->type == 'ADMIN') {
            $users = User::where('id', '!=', Auth::user()->id)->where('type', '!=', 'SUPERADMIN')->paginate(20);
        } else {
            $users = User::where('id', '!=', Auth::user()->id)
            ->where('type', '!=', 'SUPERADMIN')
            ->where('type', '!=', 'ADMIN')
            ->paginate(20);
        }

        $auth_user = Auth::user();

        if ($auth_user->type == 'MANAGER' && $auth_user->type == 'ADMIN') {
            $admin_types = AccessAction::where('name', '!=', 'SUPERADMIN')
                ->where('name', '!=', 'ADMIN')
                ->where('name', '!=', 'MANAGER')
                ->orderBy('name')->get();
        } else {
            $admin_types = AccessAction::where('name', '!=', 'SUPERADMIN')
                ->where('name', '!=', 'ADMIN')
                ->orderBy('name')
                ->get();
        }

        return view('users.index', compact('users','admin_types'));
    }

    public function viewAdd(Request $request)
    {
        $auth_user = Auth::user();

        if ($auth_user->type == 'MANAGER') {
            $admin_types = AccessAction::where('name', '!=', 'SUPERADMIN')
                ->where('name', '!=', 'MANAGER')
                ->orderBy('name')->get();
        } else {
            $admin_types = AccessAction::where('name', '!=', 'SUPERADMIN')->orderBy('name')->get();
        }
        return view('users.sections.add', compact('admin_types'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'branch' => $request->branch,
            'username' => $request->username,
            'type' => $request->type,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('users.index')->with('success', 'User is successfully created.');
    }

    public function viewUpdate(Request $request)
    {
        return abort(500);
        $user = User::where('id', '=', $request->user_id)->first();
        if ($user) {
            return view('users.sections.update', compact('user'));
        }
        return redirect()->route('users.index')->with('error', 'User does not exist.');
    }

    public function update(Request $request)
    {
        return abort(500);
    }

    public function delete(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User is successfully removed.');
        }

        return redirect()->route('users.index')->with('error', 'User does not exist.');
    }

    public function reset(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('users.index')->with('success', 'User is successfully reset.');
        }

        return redirect()->route('users.index')->with('error', 'User does not exist.');
    }
}
