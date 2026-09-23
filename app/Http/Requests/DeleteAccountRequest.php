<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    /**
     * Named error bag for Breeze/Blade modal compatibility.
     */
    protected $errorBag = 'userDeletion';

    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->user());
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}