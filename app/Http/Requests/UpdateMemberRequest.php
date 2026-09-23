<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends StoreMemberRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $memberId = $this->route('member')->id ?? null;

        // Ensure unique email rule ignores the updated member ID
        $rules['email'] = 'required|email|unique:members,email,' . $memberId;

        return $rules;
    }
}