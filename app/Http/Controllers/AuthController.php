<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;

class AuthController extends Controller
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        //
    }

    public function register()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'required|unique:users,email',
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
        ]);
    }

    public function logout()
    {
        /**
         * @var $user User
         */
        $user = $this->request->user();
        $user->invalidateAllTokens();

        return response()->json([
            'success' => true
        ]);
    }

    //
}