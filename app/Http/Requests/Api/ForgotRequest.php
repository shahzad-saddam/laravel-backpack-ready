<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ForgotRequest extends FormRequest
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
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => trans('app.user.email'),
        ];
    }
}