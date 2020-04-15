<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        /**
         * @var $user User
         */
        $user = User::currentUser();

        $user->load([
            'folders',
        ]);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
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
}
