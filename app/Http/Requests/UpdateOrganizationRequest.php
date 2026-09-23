<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');

        return $this->user()->can('update', $organization);
    }

    public function rules(): array
    {
        $organizationId = $this->route('organization')->id;

        return [
            'region_id' => 'required|exists:regions,id',
            'name'      => 'required|string|max:255|unique:organizations,name,' . $organizationId,
            'acronym'   => 'nullable|string|max:50',
            'category'  => 'required|in:LGU,PO,NGO,Academe',
        ];
    }
}