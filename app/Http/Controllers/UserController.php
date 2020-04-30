<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SuperUserMiddleware;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserController extends Controller
{

    public function index()
    {
        $users = User::query()->paginate(10);

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function view($id)
    {
        /**
         * @var $user User
         */
        $user = User::with([
            'folders',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function create()
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
                return response()->json([
                    'success' => true,
                    'user' => $user->toArray()
                ]);
            } else {
                $validator->getMessageBag()->add('general', 'Failed to save record.');
            }
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ])->setStatusCode(400);
    }

    public function update($id)
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'first_name' => 'min:2',
            'middle_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'email|unique:users,email,'.$id,
            'password' => 'confirmed',
        ]);

        if($validator->passes()) {
            /**
             * @var $user User
             */
            $user = User::findOrFail($id);

            $user->fill($this->request->input());
            $user->setPassword($this->request->input('password'));

            if($user->save()) {
                return response()->json([
                    'success' => true,
                    'user' => $user->toArray()
                ]);
            } else {
                $validator->getMessageBag()->add('general', 'Failed to save record.');
            }
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ])->setStatusCode(400);
    }

    public function delete($id)
    {
        /**
         * @var User $user
         */
        $user = User::findOrFail($id);

        if($user->superuser) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'general' => 'A superuser cannot be deleted.'
                ]
            ]);
        }

        if($user->delete()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'general' => 'Failed to delete record'
            ]
        ]);
    }
}
