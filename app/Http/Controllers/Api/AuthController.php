<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

;

class AuthController extends ApiController
{
    use ThrottlesLogins;

    /**
     * @param Request $request
     *
     * @return User
     */
    public function show(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return $user;
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            $this->username() => 'required',
            'password'        => 'required'
        ], [], [
            $this->username() => trans('app.users.' . $this->username()),
            'password'        => trans('app.users.password')
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            return response()->json([
                'status'   => $this->TOOMANYREQUESTS,
                'response' => [
                    $this->username() => trans('auth.throttle', ['seconds' => $seconds])
                ]
            ], $this->TOOMANYREQUESTS);
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;

            return response()->json([
                'status'   => $this->SUCCESS,
                'response' => $success
            ], $this->SUCCESS);

        } else {
            $this->incrementLoginAttempts($request);

            return response()->json([
                'status'   => $this->MISSING_REQUIRED_INPUTS,
                'response' => 'Unauthorised'
            ], $this->MISSING_REQUIRED_INPUTS);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|confirmed|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => $this->UNPROCESSABLE,
                'response' => $validator->errors(),
                'reason'   => $this->getReason($validator->messages()),
            ], $this->UNPROCESSABLE);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json([
            'status'   => $this->SUCCESS,
            'response' => $success
        ], $this->SUCCESS);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    private function username()
    {
        return 'email';
    }
}
