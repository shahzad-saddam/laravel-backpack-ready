<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => trans('models/user.email'),
            'password' => trans('models/user.password'),
        ];
    }
}