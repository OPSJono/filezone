<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function index()
    {
        /**
         * @var $user User
         */
        $user = User::currentUser();

        $user->load([
            'folders',
        ]);

        $this->apiResponse->setSuccess(['user' => $user]);

        return $this->apiResponse->returnResponse();
    }

    public function update()
    {
        /**
         * @var $user User
         */
        $user = User::currentUser();

        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'first_name' => 'min:2',
            'middle_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'confirmed',
        ]);

        if($validator->passes()) {
            $user->fill($this->request->input());
            $user->setPassword($this->request->input('password'));

            if($user->save()) {
                $this->apiResponse->setSuccess(['user' => $user->toArray()]);
            } else {
                $this->apiResponse->setGeneralError('Failed to save record');
            }
        } else {
            $this->apiResponse->handleErrors($validator);
        }

        return $this->apiResponse->returnResponse();
    }
}
