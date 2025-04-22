<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'   => ['nullable', 'string', 'max:255'],
            'lastname'     => ['nullable', 'string', 'max:255'],
            'balance'      => ['nullable', 'numeric', 'min:0'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'address'      => ['nullable', 'string'],
            'phones'       => ['nullable', 'array'],
            'phones.*'     => ['required', 'string', 'distinct', 'regex:/^\+?[0-9]{7,15}$/'],
        ];
    }
}
