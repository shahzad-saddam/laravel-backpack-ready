<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id(), 'id')
            ],
            'country_code' => 'required|cca2',
            'password' => 'sometimes|nullable|string|confirmed|min:8'
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('models/user.name'),
            'email' => trans('models/user.email'),
            'country_code' => trans('models/user.country_code'),
            'password' => trans('models/user.password'),
            'password_confirmation' => trans('models/user.password_confirmation'),
        ];
    }
}