<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use Carbon\Carbon;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @property int id
 * @property Boolean superuser
 *
 * Class User
 * @package App\Models
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, HasApiTokens, Authenticatable, Authorizable, MustVerifyEmail, Notifiable;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $passwordResetUrl = '';

    /**
     * Send the email verification notification.
     *
     * Override the trait method so we can pass our own version of Verify Email
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @return void
     */
    public function sendPasswordResetNotification()
    {
        $this->notify(new PasswordReset);
    }

    /**
     * @return User|\Illuminate\Contracts\Auth\MustVerifyEmail
     */
    public static function currentUser(): User
    {
        /**
         * @var Request $request
         */
        $request = app()->make('request');
        $user = $request->user();

        if(is_null($user)) {
            $user = new User([
                'id' => 0,
                'first_name' => 'dummy',
                'last_name' => 'user',
                'email' => 'dummyuser@test.com',
                'email_verified_at' => null
            ]);
        }

        return $user;
    }

    public function listOwnedFolders(): Collection
    {
        return $this->folders;
    }

    public function folders()
    {
        return $this->hasMany(Folder::class, 'created_by', 'id');
    }

    public function can($ability, $arguments = [])
    {
        if($this->superuser === 1) {
            return true;
        }

        return parent::can($ability, $arguments = []);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = htmlspecialchars(trim(strtolower($value)));
    }

    public function setPassword(string $password): void
    {
        if(!empty($password)) {
            $this->password = app()->make('hash')->make($password);
        }
    }

    public function setPasswordResetUrl(string $url): void
    {
        if(!empty($url)) {
            $this->passwordResetUrl = trim($url);
        }
    }

    /**
     * @return string
     */
    public function getPasswordResetUrl(): string
    {
        if(!empty($this->passwordResetUrl)) {
            return $this->passwordResetUrl;
        }

        return '';
    }

    public function invalidateAllTokens(): void
    {
        $this->tokens()->where('revoked', false) // Ignore any already revoked tokens
            ->whereNull('name') // Ignore personal access tokens
            ->each(function ($token) {
            /**
             * @var $token Model
             */
            $token->revoked = true;
            $token->expires_at = Carbon::now();
            $token->save();
        });
    }

    /**
     * @param string $username
     * @return User
     * @throws AuthorizationException
     */
    public function findForPassport($username)
    {
        $email = htmlspecialchars(trim(strtolower($username)));

        /**
         * @var User $user
         */
        $user = User::whereRaw("LOWER(email) = '{$email}'")->first();

        if(!$user instanceof User) {
            throw new AuthorizationException("Invalid Email, Username or Password.", 403);
        }

        if(is_null($user->email_verified_at)) {
            throw new AuthorizationException("You must verify your email address before you can login.", 403);
        }

        return $user;
    }

    /**
     * @param string $password
     * @return bool
     * @throws AuthorizationException
     */
    public function validateForPassportPasswordGrant($password)
    {
        if(!password_verify($password, $this->password)) {
            throw new AuthorizationException("Invalid Email, Username or Password.", 403);
        }

        return true;
    }
}
