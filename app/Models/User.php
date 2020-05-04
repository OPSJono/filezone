<?php

namespace App\Models;

use Carbon\Carbon;
use App\Notifications\VerifyEmail;
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

    public function setPassword(string $password): void
    {
        if(!empty($password)) {
            $this->password = app()->make('hash')->make($password);
        }
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
}
