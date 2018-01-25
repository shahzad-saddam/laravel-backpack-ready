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
            'email' => trans('app.user.email'),
            'token' => trans('app.user.reset_token'),
            'password' => trans('app.user.password'),
            'password_confirmation' => trans('app.user.password_confirmation'),
        ];
    }
}