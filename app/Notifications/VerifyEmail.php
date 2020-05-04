<?php

namespace App\Notifications;

use DateTimeInterface;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\HasApiTokens;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * Overriding the base method here as `URL::temporarySignedRoute` doesn't exist in Lumen's version of the URLGenerator.
     *
     * @param  Notifiable|HasApiTokens|MustVerifyEmail|Model $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return $this->temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Format the array of URL parameters.
     *
     * Ported over from Laravel.
     *
     * @param  mixed|array  $parameters
     * @return array
     */
    private function formatParameters($parameters)
    {
        $parameters = Arr::wrap($parameters);

        foreach ($parameters as $key => $parameter) {
            if ($parameter instanceof UrlRoutable) {
                $parameters[$key] = $parameter->getRouteKey();
            }
        }

        return $parameters;
    }

    /**
     * Create a temporary signed route URL for a named route.
     *
     * Ported over from Laravel.
     *
     * @param  string  $name
     * @param  DateTimeInterface  $expiration
     * @param  array  $parameters
     * @param  bool  $secure
     * @return string
     */
    private function temporarySignedRoute($name, $expiration, $parameters = [], $secure = null)
    {
        if(is_null($secure)) {
            $secure = config('server.https', false);
        }

        $parameters = $this->formatParameters($parameters);

        if (array_key_exists('signature', $parameters)) {
            throw new InvalidArgumentException(
                '"Signature" is a reserved parameter when generating signed routes. Please rename your route parameter.'
            );
        }

        if ($expiration) {
            $parameters = $parameters + ['expires' => $expiration->getTimestamp()];
        }

        ksort($parameters);

        $key = Arr::get($parameters, 'id', null);

        if(is_null($key)) {
            throw new InvalidArgumentException("Route Unique ID not passed to signed route generator.", 500);
        }

        return route($name,
            $parameters + [
                'signature' => hash_hmac('sha256', route($name, $parameters, $secure), $key),
            ],
            $secure
        );
    }
}
