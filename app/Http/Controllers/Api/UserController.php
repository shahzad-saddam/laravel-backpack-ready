<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Image;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends ApiController
{

    /**
     * @param Request $request
     *
     * @return User
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status'   => $this->SUCCESS,
            'response' => $user
        ], $this->SUCCESS);
    }

    /**
     * @param Request $request
     *
     * @return User
     */
    public function update(Request $request)
    {
        try {

            /** @var User $user */
            $user = Auth::user();

            $rules = [
                'name'     => 'required',
                'email'    => "required|email|unique:users,email,{$user->getKey()},{$user->getKeyName()}",
                'password' => 'sometimes|nullable|confirmed|min:8'
            ];

            $validator = Validator::make($this->inputs, $rules);

            if ($validator->fails()) {
                \Log::info('User update failed');

                return response()->json(
                    [
                        'status'   => $this->EXPECTATION_FAILED,
                        'response' => $validator->messages(),
                        'reason'   => $this->getReason($validator->messages()),
                    ], $this->EXPECTATION_FAILED
                );
            } else {

                $user->name = $request->input('name');
                $user->email = $request->input('email');

                if ($password = $request->input('password')) {
                    $user->password = bcrypt($password);
                }


                $user->saveOrFail();

                return response()->json([
                    'status'   => $this->SUCCESS,
                    'response' => $user
                ], $this->SUCCESS);
            }


        } catch (\Exception $ex) {

            $errorMessage = $ex->getMessage();
            \Log::error($errorMessage);
            $this->sendUnknownError($errorMessage);

        }
    }
}