<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', (string) $request->string('role'));
        }

        $users = $query->paginate(25)->withQueryString();

        return view('master.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('master.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['user', 'admin', 'master_admin'])],
        ]);

        if (
            $user->role === 'master_admin' &&
            $validated['role'] !== 'master_admin' &&
            User::query()->where('role', 'master_admin')->count() <= 1
        ) {
            return back()->withErrors(['role' => 'You cannot remove the last master admin.']);
        }

        if ($user->id === Auth::id() && $validated['role'] !== 'master_admin') {
            return back()->withErrors(['role' => 'You cannot remove your own master admin role.']);
        }

        $user->update($validated);

        return redirect()->route('master.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->role === 'master_admin' && User::query()->where('role', 'master_admin')->count() <= 1) {
            return back()->withErrors(['user' => 'You cannot delete the last master admin.']);
        }

        $user->delete();

        return redirect()->route('master.users.index')->with('success', 'User deleted.');
    }
}

