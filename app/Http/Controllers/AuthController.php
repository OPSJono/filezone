<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class AuthController extends Controller
{
    public function register()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validator->passes()) {
            $user = new User();
            $user->fill($this->request->input());
            $user->setPassword($this->request->input('password'));

            if($user->save()) {
                $this->apiResponse->setSuccess($user);
            } else {
                $this->apiResponse->setGeneralError("Failed to save record.");
            }
        } else {
            $this->apiResponse->handleErrors($validator);
        }

        return $this->apiResponse->returnResponse();
    }

    public function logout()
    {
        /**
         * @var $user User
         */
        $user = User::currentUser();

        if($user instanceof User) {
            $user->invalidateAllTokens();
        }

        return $this->apiResponse->returnResponse();
    }
}
