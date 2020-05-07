<?php

namespace App\Notifications;

use DateTimeInterface;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Laravel\Passport\HasApiTokens;

abstract class BaseNotification extends \Illuminate\Auth\Notifications\VerifyEmail
{

    /**
     * Format the array of URL parameters.
     *
     * Ported over from Laravel.
     *
     * @param  mixed|array  $parameters
     * @return array
     */
    protected function formatParameters($parameters)
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
    protected function temporarySignedRoute($name, $expiration, $parameters = [], $secure = null)
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
