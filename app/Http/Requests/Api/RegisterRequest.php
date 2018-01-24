<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return !Auth::check();
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
                Rule::unique('users', 'email')
            ],
            'country_code' => 'required|cca2',
            'password' => 'required|string|confirmed|min:8'
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('app.user.name'),
            'email' => trans('app.user.email'),
            'country_code' => trans('app.user.country_code'),
            'password' => trans('app.user.password'),
            'password_confirmation' => trans('app.user.password_confirmation'),
        ];
    }
}