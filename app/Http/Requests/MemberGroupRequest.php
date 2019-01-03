<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberGroupRequest extends FormRequest
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
            'title' => 'required',
            'params' => 'required',
        ];
    }
    
    public function messages()
    {
        return [
            'title.required' => '请填写级别名称',
            'params.required' => '请设置分成参数'
        ];
    }

    public function all()
    {
        $data = parent::all();
        return $data;
    }
}
