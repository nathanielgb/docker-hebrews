<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccessAction;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $admin_types = [];

        if (Auth::user()->type == 'SUPERADMIN') {
            $users = User::where('id', '!=', Auth::user()->id);
        } else if (Auth::user()->type == 'ADMIN') {
            $users = User::where('id', '!=', Auth::user()->id)
                ->where('type', '!=', 'SUPERADMIN');
        } else {
            $users = User::where('id', '!=', Auth::user()->id)
            ->where('type', '!=', 'SUPERADMIN')
            ->where('type', '!=', 'ADMIN');
        }

        $branches = Branch::all('id','name')->toArray();

        if (Auth::user()->branch_id != null) {
            $users = $users->where('branch_id', Auth::user()->branch_id);
            $branches = Branch::where('id', Auth::user()->branch_id)->get()->toArray();
        }

        $auth_user = Auth::user();
        if ($auth_user->type == 'SUPERADMIN') {
            $admin_types = AccessAction::orderBy('name')->get();
        } else if ($auth_user->type == 'ADMIN') {
            $admin_types = AccessAction::where('name', '!=', 'SUPERADMIN')
                ->orderBy('name')->get();
        } else if ($auth_user->type == 'MANAGER') {
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

        $users = $users->paginate(20);


        return view('users.index', compact('users','admin_types','branches'));
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
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type' => ['required'],
        ]);

        // if (!auth()->user()->type == 'SUPERADMIN' && !auth()->user()->type == 'ADMIN') {
        //     if (!$request->branch) {
        //         return redirect()->back()->with('error', 'Failed to add user. Branch is required.');
        //     }

        //     // Check if branch exist
        //     if (Branch::whereIn('id', $request->branch_id)->count() <= 0) {
        //         return redirect()->back()->with('error', 'Failed to add user. Branch does not exist please try again.');
        //     }
        // }

        if ($request->branch_id) {
            // Check if branch exist
            if (Branch::where('id', $request->branch_id)->count() <= 0) {
                return redirect()->back()->with('error', 'Failed to add user. Branch does not exist please try again.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
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

    public function delete(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User is successfully removed.');
        }

        return redirect()->route('users.index')->with('error', 'User does not exist.');
    }

    public function update(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            $request->validate([
                'type' => ['required'],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]);
            if ($request->branch_id) {
                // Check if branch exist
                if (Branch::where('id', $request->branch_id)->count() <= 0) {
                    return redirect()->back()->with('error', 'Failed to add user. Branch does not exist please try again.');
                }
            }
            if ($request->password) {
                $user->update([
                    'type' => $request->type,
                    'branch_id' => $request->branch_id,
                    'password' => Hash::make($request->password)
                ]);
            } else {
                $user->update([
                    'type' => $request->type,
                    'branch_id' => $request->branch_id
                ]);
            }


            return redirect()->route('users.index')->with('success', 'User is successfully updated.');
        }

        return redirect()->route('users.index')->with('error', 'User does not exist.');
    }


    public function viewBranches(Request $request)
    {
        $branches = Branch::paginate(20);

        return view('users.branches', compact('branches'));
    }

    public function addBranch(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches')->where('name', $request->input('name'))
            ],
            'branch' => ['nullable', 'string', 'max:255'],
        ]);

        Branch::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        return redirect()->back()->with('success', 'Branch is successfully created.');
    }

    public function updateBranch(Request $request)
    {
        $branch = Branch::where('id', $request->branch_id)->first();

        if ($branch) {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('branches', 'name')->ignore($branch->id, 'id'),
                    ],
                'branch' => ['nullable', 'string', 'max:255'],
            ]);

            $branch->update([
                'name' => $request->name,
                'location' => $request->location,
            ]);

            return redirect()->back()->with('success', 'Branch is successfully updated.');
        }

        return redirect()->back()->with('error', 'Error updating branch. Record does not exist.');
    }

    public function deleteBranch(Request $request)
    {
        $branch = Branch::where('id', $request->branch_id)->first();

        if ($branch) {
            $branch->delete();
            return redirect()->back()->with('success', 'Branch is successfully removed.');
        }

        return redirect()->back()->with('error', 'Error deleting branch. Record does not exist.');
    }

}
