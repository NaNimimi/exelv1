<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionRequest extends FormRequest
{
    public function authorize()
    {
        return auth('web')->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'permission_ids.required' => 'Kamida bitta ruxsat tanlanishi shart.',
            'permission_ids.*.exists' => 'Tanlangan ruxsat mavjud emas.',
        ];
    }
}