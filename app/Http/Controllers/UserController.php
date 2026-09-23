<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $authUser = auth()->user();

        $users = User::with('region')
            ->when(
                in_array($authUser->role, ['President', 'Coordinator']),
                fn ($query) => $query->where('region_id', $authUser->region_id)
            )
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $authUser = auth()->user();

        $regions = $authUser->role === 'Admin'
            ? Region::all()
            : Region::where('id', $authUser->region_id)->get();

        return view('users.create', compact('regions'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_accepted'] = true;

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function accept(User $user)
    {
        $this->authorize('accept', $user);

        $user->update([
            'is_accepted' => true,
            'accepted_by' => auth()->id(),
            'accepted_at' => now(),
        ]);

        return back()->with('success', "Access granted for {$user->name}.");
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $regions = Region::all();

        return view('users.edit', compact('user', 'regions'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($validated['role'] === 'Admin') {
            $validated['region_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Updated {$user->name} to {$validated['role']}.");
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}