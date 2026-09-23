<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    protected function prepareForValidation(): void
    {
        $authUser = $this->user();

        // Lock regional scope for Presidents/Coordinators
        if ($authUser->role !== 'Admin') {
            $this->merge([
                'region_id' => $authUser->region_id,
            ]);
        }
    }

    public function rules(): array
    {
        $authUser = $this->user();

        // Admin can assign any role; Presidents/Coordinators are restricted
        $allowedRoles = $authUser->role === 'Admin'
            ? ['Admin', 'President', 'Coordinator', 'Member']
            : ['Coordinator', 'Member'];

        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::defaults()],
            'role'      => ['required', Rule::in($allowedRoles)],
            'region_id' => [
                $authUser->role === 'Admin' ? 'required_if:role,Coordinator,President,Member' : 'nullable',
                'exists:regions,id',
            ],
            'position'  => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation callback for duplicate role/position checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $regionId = $this->input('region_id');
            $role = $this->input('role');
            $position = $this->input('position');

            if (!$regionId) {
                return;
            }

            $exists = User::where('region_id', $regionId)
                ->where('role', $role)
                ->where('position', $position)
                ->whereNotNull('region_id')
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'role',
                    "This region already has an assigned {$role} with position '{$position}'."
                );
            }
        });
    }
}