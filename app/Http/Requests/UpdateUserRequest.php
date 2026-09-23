<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');

        return $this->user()->can('update', $targetUser);
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'role'      => ['required', Rule::in(['Admin', 'President', 'Coordinator', 'Member'])],
            'region_id' => ['nullable', 'required_unless:role,Admin', 'exists:regions,id'],
        ];
    }
}