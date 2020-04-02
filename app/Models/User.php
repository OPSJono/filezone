<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

/**
 * @method static create(array $array)
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

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
     * @var array
     */
    protected $appends = [
        'passport_token'
    ];

    public function can($ability, $arguments = [])
    {
        if($this->superuser === 1) {
            return true;
        }

        return parent::can($ability, $arguments = []);
    }

    public function getPassportTokenAttribute()
    {
        return $this->token();
    }

    public function setPassword(string $password): void
    {
        $this->password = app()->make('hash')->make($password);
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
