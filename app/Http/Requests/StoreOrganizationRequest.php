<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Organization::class);
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();
        $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin') || strtolower($user->role ?? '') === 'admin';

        // Enforce region constraint for non-admins before validation runs
        if (!$isAdmin) {
            $this->merge([
                'region_id' => $user->region_id,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'acronym'       => 'nullable|string|max:20',
            'category'      => 'required|in:LGU,PO,NGO,Academe',
            'region_id'     => 'required|exists:regions,id',
            'certification' => 'nullable|file|mimes:pdf|max:5120',
        ];
    }
}