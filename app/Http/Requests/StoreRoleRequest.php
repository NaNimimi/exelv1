<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize()
    {
        return auth('web')->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Rol nomi kiritilishi shart.',
            'name.unique' => 'Bu rol nomi allaqachon mavjud.',
            'permission_ids.*.exists' => 'Tanlangan ruxsat mavjud emas.',
        ];
    }
}