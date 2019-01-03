<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|unique:admins|max:40',
            'name' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '必须填写用户名',
            'username.unique' => '用户名已存在，请重新选择',
            'username.max' => '用户名不能超过40个字符',
            'name.required' => '必须填写姓名',
            'password.required' => '必须填写密码',
        ];
    }
}
