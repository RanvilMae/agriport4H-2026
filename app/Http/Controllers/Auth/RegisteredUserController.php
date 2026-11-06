<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Region;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Fetch all regions to populate the dropdown in the register view
        $regions = Region::orderBy('id', 'asc')->get();

        return view('auth.register', compact('regions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate incoming fields (member_id is generated automatically
        //    by the User model, so the form no longer sends it)
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role'      => ['required', 'in:Admin,Coordinator,Member,President'],
            'region_id' => ['required_if:role,Coordinator,President,Member', 'nullable', 'exists:regions,id'],
            'position'  => ['nullable', 'string', 'max:255'],
        ]);

        // 2. Prevent duplicate role/position assignments in the same region
        if ($request->filled('region_id')) {
            $exists = User::where('region_id', $request->region_id)
                ->where('role', $request->role)
                ->where('position', $request->position)
                ->exists();

            if ($exists) {
                return back()->withInput()->withErrors([
                    'role' => "This region already has an assigned {$request->role} for this position."
                ]);
            }
        }

        // 3. Create the user AND their member profile together.
        //    User::booted() assigns the Member ID (e.g. 4H-I-2026-000-001).
        //    The transaction means that if the profile fails to save, the user
        //    is not created either, so we never end up with half an account.
        //    If two people register at the same instant and get the same ID,
        //    the unique index rejects one and we retry with the next number.
        $user = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $user = DB::transaction(function () use ($request) {
                    $user = User::create([
                        'name'      => $request->name,
                        'email'     => $request->email,
                        'password'  => Hash::make($request->password),
                        'role'      => $request->role,
                        'region_id' => $request->region_id,
                        'position'  => $request->position,
                    ]);

                    [$firstName, $lastName] = self::splitName($request->name);

                    // Starter profile; the member completes the rest on
                    // the profile edit page.
                    Member::forceCreate([
                        'user_id'    => $user->id,
                        'member_id'  => $user->member_id,
                        'first_name' => $firstName,
                        'last_name'  => $lastName,
                        'email'      => $user->email,
                        'region_id'  => $user->region_id,
                    ]);

                    return $user;
                });
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === 3) {
                    throw $e;
                }
            }
        }

        event(new Registered($user));

        // Redirect back to the registration page with the success message
        return redirect()->route('register')
            ->with('success', "Account registered! Your Member ID is {$user->member_id}. Contact your president for activation of your account.");
    }

    /**
     * Split a full name into [first name, last name].
     *
     * Handles Filipino surname particles, so "Juan Dela Cruz" becomes
     * ["Juan", "Dela Cruz"] and "Maria Clara Santos" becomes
     * ["Maria Clara", "Santos"].
     */
    public static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));

        if (count($parts) === 1) {
            return [$parts[0], ''];
        }

        $particles = ['de', 'dela', 'del', 'della', 'delos', 'san', 'santa', 'sta.', 'sto.', 'santo', 'la', 'los'];

        // Surname starts at the first particle after the first word, if any
        for ($i = 1; $i < count($parts) - 1; $i++) {
            if (in_array(strtolower($parts[$i]), $particles, true)) {
                return [
                    implode(' ', array_slice($parts, 0, $i)),
                    implode(' ', array_slice($parts, $i)),
                ];
            }
        }

        // Otherwise the last word is the surname
        return [
            implode(' ', array_slice($parts, 0, -1)),
            end($parts),
        ];
    }
}