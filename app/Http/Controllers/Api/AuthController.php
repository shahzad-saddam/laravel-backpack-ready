<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ForgotRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetRequest;
use App\Http\Requests\Api\ValidationRequest;
use App\Http\Requests\Api\ValidationResendRequest;
use App\Mail\EmailValidation;
use App\Mail\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));

            return new JsonResponse([
                'error' => 'too_many_attempts',
                'expires_in' => $seconds,
                'expires' => Carbon::now()->addSeconds($seconds)->format(\DateTime::ATOM),
            ], JsonResponse::HTTP_LOCKED);
        }

        if ($token = $this->guard()->attempt($request->only('email', 'password'))) {
            $this->clearLoginAttempts($request);

            /** @var User $user */
            $user = $this->guard()->user();
            if (!$user->validated) {
                return new JsonResponse([
                    'error' => 'not_validated',
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }

            return $this->respondWithToken($token);
        }

        $this->incrementLoginAttempts($request);

        return new JsonResponse([
            'error' => 'invalid_credentials',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            $this->guard()->logout();
        } catch (\Throwable $ignored) {
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function refresh()
    {
        try {
            return $this->respondWithToken($this->guard()->refresh());
        } catch (\Throwable $ignored) {
        }

        return new JsonResponse([
            'error' => 'invalid_token',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $attributes = $request->only(['name', 'email', 'country_code']);
        $attributes['password'] = Hash::make($request->input('password'));

        $user = User::create($attributes);
        if (!$user) {
            abort(500);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param ForgotRequest $request
     * @return JsonResponse
     */
    public function forgot(ForgotRequest $request)
    {
        $attributes = $request->only(['email']);

        /** @var PasswordBrokerManager $password */
        $password = App::make('auth.password');

        /** @var PasswordBroker $passwordBroker */
        $passwordBroker = $password->broker();

        /** @var User $user */
        $user = $passwordBroker->getUser($attributes);
        if (!$user) {
            return new JsonResponse([
                'error' => 'user_not_found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$user->validated) {
            return new JsonResponse([
                'error' => 'not_validated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        Mail::to([$user])->queue(new PasswordReset($user, $passwordBroker->createToken($user)));

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param ResetRequest $request
     * @return JsonResponse
     */
    public function reset(ResetRequest $request)
    {
        $attributes = $request->only(['email', 'token', 'password', 'password_confirmation']);

        /** @var PasswordBrokerManager $password */
        $password = App::make('auth.password');

        /** @var PasswordBroker $passwordBroker */
        $passwordBroker = $password->broker();

        $result = $passwordBroker->reset($attributes, function (User $user, $password) {
            $user->update(['password' => Hash::make($password)]);
        });

        if ($result == PasswordBroker::INVALID_USER) {
            return new JsonResponse([
                'error' => 'user_not_found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($result == PasswordBroker::INVALID_PASSWORD) {
            return new JsonResponse([
                'error' => 'invalid_password',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($result == PasswordBroker::INVALID_TOKEN) {
            return new JsonResponse([
                'error' => 'invalid_token',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $passwordBroker->getUser($attributes);

        return $this->respondWithToken($this->guard()->login($user));
    }

    /**
     * @param ValidationRequest $request
     * @return JsonResponse
     */
    public function validateEmail(ValidationRequest $request)
    {
        $user = User::where('email', $request->input('email'))->firstOrFail();

        if (!Hash::check($request->input('token'), $user->validation_code)) {
            return new JsonResponse([
                'error' => 'invalid_token',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$user->update(['validated' => true])) {
            abort(500);
        }

        return $this->respondWithToken($this->guard()->login($user));
    }

    /**
     * @param ValidationResendRequest $request
     * @return JsonResponse
     */
    public function validateResend(ValidationResendRequest $request)
    {
        $user = User::where('email', $request->input('email'))->firstOrFail();

        if ($user->validated) {
            return new JsonResponse([
                'error' => 'already_validated'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = Str::random(10);
        if (!$user->update(['validation_code' => Hash::make($token)])) {
            abort(500);
        }

        Mail::to([$user])->queue(new EmailValidation($user, $token));

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        /** @var JWT $jwt */
        $jwt = App::make('tymon.jwt');

        $jwt->setToken($token);

        $payload = $jwt->getPayload();

        return new JsonResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires' => Carbon::createFromTimestamp($payload->get('exp'))->format(\DateTime::ATOM),
            'expires_in' => $jwt->factory()->getTTL() * 60,
        ]);
    }

    /**
     * @return \Tymon\JWTAuth\JWTGuard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    /**
     * @return string
     */
    protected function username()
    {
        return 'email';
    }
}