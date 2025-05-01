<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleToUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth('web')->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'role_id.required' => 'Rol tanlanishi shart.',
            'role_id.exists' => 'Tanlangan rol mavjud emas.',
        ];
    }
}