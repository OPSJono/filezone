<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * @acl public
     *
     * @return JsonResponse
     */
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
                $user->sendEmailVerificationNotification();
                $this->apiResponse->setSuccess($user);
            } else {
                $this->apiResponse->setGeneralError("Failed to save record.");
            }
        } else {
            $this->apiResponse->handleErrors($validator);
        }

        return $this->apiResponse->returnResponse();
    }

    /**
     * @acl auth
     *
     * @return JsonResponse
     */
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

    /**
     * @acl public
     *
     * @return JsonResponse
     */
    public function requestEmailVerification()
    {
        $user_id = $this->request->input('user_id', null);

        if(is_null($user_id)) {
            $this->apiResponse->setError("You must specify a `user_id` parameter");
        }

        /**
         * @var User $user
         */
        $user = User::find($user_id);

        if(!$user instanceof User) {
            $this->apiResponse->setError("No user with that ID found.");
        } else {
            if ($user->hasVerifiedEmail()) {
                $this->apiResponse->setResponseCode(400);
                $this->apiResponse->setGeneralError("Your email is already verified");
            } else {
                $user->sendEmailVerificationNotification();
                $this->apiResponse->setSuccess(['sent' => true]);
            }
        }

        return $this->apiResponse->returnResponse();
    }

    /**
     * Send a password reset email to the user.
     *
     * @acl public
     *
     * @return JsonResponse
     */
    public function requestPasswordReset()
    {
        $email = $this->request->input('email', null);

        if(is_null($email)) {
            $this->apiResponse->setError("You must specify the `email` parameter");
        }

        $email = htmlspecialchars(trim(strtolower($email)));

        /**
         * @var User $user
         */
        $user = User::where('email', $email)->first();

        if(!$user instanceof User) {
            $this->apiResponse->setError("No user with that Email found.");
        } else {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            if($this->request->has('callback_url') && $this->request->filled('callback_url')) {
                $user->setPasswordResetUrl($this->request->input('callback_url'));
            }

            $user->sendPasswordResetNotification();
            $this->apiResponse->setSuccess(['sent' => true]);
        }

        return $this->apiResponse->returnResponse();
    }

    /**
     * Reset the user password based on input.
     *
     * @acl public
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function resetPassword()
    {
        /**
         * @var User|MustVerifyEmail $user
         */
        $user = User::findOrFail($this->request->input('id', null));

        if(!$user instanceof User) {
            $this->apiResponse->setError("No user with that ID found.");
        } else {
            if (! hash_equals((string) $this->request->input('id'), (string) $user->getKey())) {
                throw new AuthorizationException;
            }

            if (! hash_equals((string) $this->request->input('hash'), sha1($user->getEmailForVerification()))) {
                throw new AuthorizationException;
            }

            /**
             * @var $validator Validator
             */
            $validator = app()->make('validator')->make($this->request->input(), [
                'password' => 'required|confirmed',
                'password_confirmation' => 'required',
            ]);

            if ($validator->passes()) {
                $user->setPassword($this->request->input('password'));
                $this->apiResponse->setSuccess(['changed' => true]);
            } else {
                $this->apiResponse->handleErrors($validator);
            }
        }

        return $this->apiResponse->returnResponse();
    }

    /**
     * Mark the user's email address as verified.
     *
     * @acl public
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function verifyEmail()
    {
        /**
         * @var User|MustVerifyEmail $user
         */
        $user = User::findOrFail($this->request->input('id', null));

        if (! hash_equals((string) $this->request->input('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $this->request->input('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            $this->apiResponse->setResponseCode(400);
            $this->apiResponse->setGeneralError("Your email is already verified");
        } elseif($user->markEmailAsVerified()) {
            event(new Verified($user));
            $this->apiResponse->setSuccess(['verified' => true]);
        }

        return $this->apiResponse->returnResponse();
    }
}
