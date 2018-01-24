<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ResetRequest extends FormRequest
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
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => trans('models/user.email'),
            'token' => trans('app.reset_token'),
            'password' => trans('models/user.password'),
            'password_confirmation' => trans('models/user.password_confirmation'),
        ];
    }
}